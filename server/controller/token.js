const axios = require("axios");

const createToken = async (req, res, next) => {
    const secret = "1G7Eyp1o8WunEm60Vca6KrjCTwxoVFbfGs8z3SfxMy0w4SQj2FHfGIsCtjbxE1YL";
    const consumer = "jAUghrNniPvG6zHACGaO8QeN6b2zQ1pcGu0v35N1u7jdbt8L";
    const auth = Buffer.from(`${consumer}:${secret}`).toString("base64");

    await axios.get(
            "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials",
            {
                headers: {
                    Authorization: `Basic ${auth}`,
                },
            }
        )
        .then((data) => {
            req.token = data.data.access_token; // Store the token in the request object
            console.log(data.data);
            next();
        })
        .catch((err) => {
            console.log(err);
            res.status(400).json({ error: err.message });
        });
};

const stkPush = async (req, res) => {
    const shortCode = 174379;
    const phone = req.body.phone.substring(1); // Remove the leading '0'
    const amount = req.body.amount;
    const passkey = "bfb279f6b8b11c69c3b3e1c1e1e1e1e1";
    const url = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";

    const date = new Date();
    const timestamp =
        date.getFullYear() +
        ("0" + (date.getMonth() + 1)).slice(-2) +
        ("0" + date.getDate()).slice(-2) +
        ("0" + date.getHours()).slice(-2) +
        ("0" + date.getMinutes()).slice(-2) +
        ("0" + date.getSeconds()).slice(-2);

    const password = Buffer.from(shortCode + passkey + timestamp).toString("base64");

    const data = {
        BusinessShortCode: shortCode,
        Password: password,
        Timestamp: timestamp,
        TransactionType: "CustomerPayBillOnline",
        Amount: amount,
        PartyA: `254${phone}`, // Corrected string interpolation
        PartyB: shortCode,
        PhoneNumber: `254${phone}`, // Corrected string interpolation
        CallBackURL: "https://example.com/callback",
        AccountReference: "Mpesa test",
        TransactionDesc: "testing stk push"
    };

    await axios.post(url, data, {
            headers: {
                Authorization: `Bearer ${req.token}`, // Use the access token from createToken
            },
        })
        .then((data) => {
            console.log(data);
            res.status(200).json(data.data);
        })
        .catch((err) => {
            console.log(err);
            res.status(400).json({ error: err.message });
        });
};

module.exports = { createToken, stkPush };