const express = require('express');
const cors = require('cors');
const productRoutes = require('./routes/productRoutes');

const app = express();

app.use(cors());

// Naikkan batas ukuran payload/body menjadi 3mb agar muat untuk image 2MB (Base64)
app.use(express.json({ limit: '3mb' }));
app.use(express.urlencoded({ limit: '3mb', extended: true }));

// endpoint for healthcheck
app.get('/health', (req, res) => {
    res.json({
        status: "ok",
        service: "product-service"
    });
});

app.use("/products", productRoutes);

// unknown path
app.use((req, res) => {
    res.status(404).json({
        message: "Endpoint tidak dikenal"
    });
});

module.exports = app;