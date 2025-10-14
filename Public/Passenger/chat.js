
document.addEventListener("DOMContentLoaded", () => {
    const inputField = document.querySelector(".chat-window .input-area input");
    const sendButton = document.querySelector(".chat-window .input-area button");
    const chatBox = document.querySelector(".chat-window .chat");

    // Rules with multiple keywords
    const rules = [
        {
            keywords: ["hello", "hi", "hey", "how are you", "morning", "afternoon", "evening", "what's up"],
            response: "👋 Hello! Welcome to Tshwane Bus Services. How can I help you today?"
        },
        {
            keywords: ["hours", "time", "schedule", "open", "close", "when", "operating", "timetable", "service time"],
            response: "🚌 Our operating hours are:\nMon–Fri: 04:15–19:45\nSat: 05:00–16:50\nSun & Public Holidays: No service."
        },
        {
            keywords: ["pay", "payment", "cash", "card", "ticket", "connector", "top up", "recharge", "buy", "purchase"],
            response: "💳 We only accept the *Connector Card* (no cash). You can buy or top it up at sales offices, depots, or selected retailers."
        },
        {
            keywords: ["refund", "money back", "lost", "damaged", "replace", "replacement", "balance", "return", "claim"],
            response: "🔄 Refund Policy:\n- Unused balances can be refunded at official TBS sales offices.\n- Lost/damaged cards can be replaced for a small fee."
        },
        {
            keywords: ["contact", "phone", "call", "number", "support", "email", "helpdesk", "help", "assistance"],
            response: "☎️ Contact Us:\nPhone: +27 12 358 9999\nEmail: tbs@tshwane.gov.za\nWebsite: www.tshwane.gov.za"
        }
    ];

    // Function to add messages to chat
    function addMessage(message, sender) {
        const messageElement = document.createElement("div");
        messageElement.classList.add(sender);
        const p = document.createElement("p");
        p.textContent = message;
        messageElement.appendChild(p);
        chatBox.appendChild(messageElement);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // Handle user input
    function handleUserInput() {
        const userInput = inputField.value.trim().toLowerCase();
        if (userInput === "") return;

        addMessage(inputField.value, "user");
        inputField.value = "";

        let botResponse = "🤔 Sorry, I didn’t understand. Try asking about *hours, payment, refunds, or contact info*.";

        // Check all rules
        for (let rule of rules) {
            for (let keyword of rule.keywords) {
                if (userInput.includes(keyword)) {
                    botResponse = rule.response;
                    break;
                }
            }
            if (botResponse !== "🤔 Sorry, I didn’t understand. Try asking about *hours, payment, refunds, or contact info*.") break;
        }

        addMessage(botResponse, "model");
    }

    // Event listeners
    sendButton.addEventListener("click", handleUserInput);
    inputField.addEventListener("keypress", (e) => {
        if (e.key === "Enter") handleUserInput();
    });

    // Open & Close chat
    document.querySelector(".chat-button").addEventListener("click", () => {
        document.querySelector("body").classList.add("chat-open");
    });

    document.querySelector(".chat-window button.close").addEventListener("click", () => {
        document.querySelector("body").classList.remove("chat-open");
    });
});