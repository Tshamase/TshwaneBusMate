const express = require("express");
const path = require("path");
const http = require("http");
const WebSocket = require("ws");

const app = express();
const server = http.createServer(app);
const wss = new WebSocket.Server({ server });

// Serve static files from 'public'
app.use(express.static(path.join(__dirname, "public")));

// Routes
app.get("/", (req, res) => {
  res.sendFile(path.join(__dirname, "public/Passenger/home.html"));
});

app.get("/driver", (req, res) => {
  res.sendFile(path.join(__dirname, "public/Driver/WestPark7-Driver.html"));
});

// Handle WebSocket connections
wss.on("connection", ws => {
  console.log("New client connected");

  ws.on("message", message => {
    console.log("Received message:", message.toString());
    wss.clients.forEach(client => {
      if (client !== ws && client.readyState === WebSocket.OPEN) {
        client.send(message.toString());
      }
    });
  });

  ws.on("close", () => console.log("Client disconnected"));
  ws.on("error", error => console.error("WebSocket error:", error));
});

// Start server
const PORT = process.env.PORT || 3000;
server.listen(PORT, "0.0.0.0", () => {
  console.log(`Server running on port ${PORT}`);
});
