/**
 * LMS WhatsApp Gateway (Baileys)
 * - Pair once: open GET /qr and scan from your WhatsApp (Linked devices)
 * - Send: POST /send { to: "2010...", message: "..." } with header x-api-key
 * - Status: GET /status
 */
const express = require('express');
const QRCode = require('qrcode');
const {
    default: makeWASocket,
    useMultiFileAuthState,
    DisconnectReason,
    fetchLatestBaileysVersion,
} = require('@whiskeysockets/baileys');

const PORT = process.env.GATEWAY_PORT || 3100;
const API_KEY = process.env.GATEWAY_API_KEY || 'change-me';
const AUTH_DIR = process.env.GATEWAY_AUTH_DIR || './auth-state';

const app = express();
app.use(express.json());

let sock = null;
let lastQr = null;
let connectedNumber = null;

function checkKey(req, res, next) {
    if (req.headers['x-api-key'] !== API_KEY) {
        return res.status(401).json({ ok: false, error: 'unauthorized' });
    }
    next();
}

async function connect() {
    const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);
    const { version } = await fetchLatestBaileysVersion();

    sock = makeWASocket({
        version,
        auth: state,
        printQRInTerminal: true,
        browser: ['LMS Dashboard', 'Chrome', '1.0'],
    });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('connection.update', (u) => {
        const { connection, lastDisconnect, qr } = u;
        if (qr) {
            lastQr = qr;
            console.log('SCAN QR: open http://localhost:' + PORT + '/qr');
        }
        if (connection === 'open') {
            lastQr = null;
            connectedNumber = sock.user ? sock.user.id.split(':')[0] : null;
            console.log('CONNECTED as', connectedNumber);
        }
        if (connection === 'close') {
            const code = lastDisconnect?.error?.output?.statusCode;
            console.log('DISCONNECTED, code:', code);
            if (code !== DisconnectReason.loggedOut) {
                setTimeout(connect, 5000);
            } else {
                console.log('LOGGED OUT — delete auth-state dir and rescan');
            }
        }
    });
}

app.get('/qr', async (req, res) => {
    if (!lastQr) {
        return res.json({ ok: true, connected: !!connectedNumber, number: connectedNumber });
    }
    res.setHeader('Content-Type', 'image/png');
    QRCode.toFileStream(res, lastQr, { width: 300 });
});

app.get('/status', (req, res) => {
    res.json({ ok: true, connected: !!connectedNumber, number: connectedNumber });
});

app.post('/send', checkKey, async (req, res) => {
    try {
        if (!sock || !connectedNumber) {
            return res.status(503).json({ ok: false, error: 'not-connected' });
        }
        let { to, message } = req.body || {};
        if (!to || !message) {
            return res.status(422).json({ ok: false, error: 'to and message required' });
        }
        const digits = String(to).replace(/\D+/g, '');
        const jid = digits + '@s.whatsapp.net';
        const [result] = await sock.onWhatsApp(jid);
        if (!result?.exists) {
            return res.status(422).json({ ok: false, error: 'number-not-on-whatsapp' });
        }
        await sock.sendMessage(jid, { text: String(message).slice(0, 2000) });
        return res.json({ ok: true });
    } catch (e) {
        console.error('SEND ERROR', e.message);
        return res.status(500).json({ ok: false, error: 'send-failed' });
    }
});

app.listen(PORT, () => {
    console.log('Gateway listening on http://localhost:' + PORT);
    connect();
});
