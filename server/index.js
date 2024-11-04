const express = require('express');
const app = express();
const TokenRoute= require('./routes/token');

app.use(express.json ());

app.use(express.urlencoded({ extended: true }));

app.get('/', (req, res) => {
    res.send('Hello, Express!');
});

const PORT = process.env.PORT || 3005;
app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});

app.get("/",(req,res)=>{
    res.send("Mpesa programming in progress,Time to get paid");
})
app.use("/token",TokenRoute);