// --- IMPORT MODULES ---
const express = require("express"); // For serving web pages
const path = require("path");       // For working with file paths
const http = require("http");       // Needed to create server for WebSocket
const WebSocket = require("ws");    // For real-time communication

// --- CREATE EXPRESS APP & HTTP SERVER ---
const app = express();
const server = http.createServer(app);

// --- CREATE WEBSOCKET SERVER ---
const wss = new WebSocket.Server({ server }); // Attach WebSocket to same HTTP server

// --- SERVE STATIC FILES ---
// All files in the 'Public' folder can be accessed directly
// Example: /Passenger/home.html or /Driver/WestPark7-Driver.html
app.use(express.static(path.join(__dirname, "Public"))); // Note: folder name is case-sensitive

// --- DEFINE ROUTES ---
// Passenger main page
app.get("/", (req, res) => {
  res.sendFile(path.join(__dirname, "Public/Passenger/home.html"));
});

// Driver page
app.get("/Driver", (req, res) => {
  res.sendFile(path.join(__dirname, "Public/Driver/WestPark7-Driver.html"));
});

// --- HANDLE WEBSOCKET CONNECTIONS ---
// When a new client (driver or passenger) connects:
wss.on("connection", ws => {
  console.log("New client connected");

  // When this client sends a message (GPS update from driver)
  ws.on("message", message => {
    console.log("Received message:", message.toString());

    // Broadcast the message to all other connected clients
    wss.clients.forEach(client => {
      // Only send to clients that are not the sender
      // and that are still connected
      if (client !== ws && client.readyState === WebSocket.OPEN) {
        client.send(message.toString());
      }
    });
  });

  // Handle client disconnect
  ws.on("close", () => console.log("Client disconnected"));

  // Handle WebSocket errors
  ws.on("error", error => console.error("WebSocket error:", error));
});

// --- START SERVER ---
// Use the port assigned by Render, or fallback to 3000 for local testing
const PORT = process.env.PORT || 3000;
server.listen(PORT, "0.0.0.0", () => {
  console.log(`Server running on port ${PORT}`);
});});

// Start server
const PORT = process.env.PORT || 3000;
server.listen(PORT, "0.0.0.0", () => {
  console.log(`Server running on port ${PORT}`);
});
