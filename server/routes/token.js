const express = require("express");
const router = express.Router();

const { createToken, stkPush } = require("../controller/token"); // Ensure both functions are imported

router.post("/", createToken, stkPush); // Use createToken and then stkPush

module.exports = router;