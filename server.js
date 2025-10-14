const express = require("express");
const path = require("path");
const http = require("http");
const WebSocket = require("ws");

const app = express();
const server = http.createServer(app);
const wss = new WebSocket.Server({ server });

// Serve static files from 'public'
app.use(express.static(path.join(__dirname, "public")));

// Handle WebSocket connections
wss.on("connection", ws => {
  console.log("New client connected");
  
  // Handle messages from this specific client
  ws.on("message", message => {
    console.log("Received message:", message.toString());
    
    // Broadcast to all connected clients
    wss.clients.forEach(client => {
      if (client !== ws && client.readyState === WebSocket.OPEN) {
        client.send(message.toString());
      }
    });
  });
  
  // Handle client disconnect
  ws.on("close", () => {
    console.log("Client disconnected");
  });
  
  // Handle errors
  ws.on("error", error => {
    console.error("WebSocket error:", error);
  });
});

const PORT = 3000;
server.listen(PORT, "0.0.0.0", () => {
  console.log(`Server running at http://Port:${PORT}`);
});