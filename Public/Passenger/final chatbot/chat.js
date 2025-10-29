document.addEventListener("DOMContentLoaded", () => {
    const inputField = document.querySelector(".chat-window .input-area input");
    const sendButton = document.querySelector(".chat-window .input-area button");
    const chatBox = document.querySelector(".chat-window .chat");
    const chatWindow = document.querySelector(".chat-window");
    const chatButton = document.querySelector(".chat-button");
    let hasAskedQuestion = false; // Track if user has asked a typed question

    // Rules 
    const rules = [
    {
        keywords: ["fare", "price", "cost", "how much", "ticket price", "charge", "band", "distance fare"],
        response: "💰 Bus Fares (effective 1 July 2025 - 30 June 2026):\n- Minimum: R13.00 (0-8 km)\n- Maximum: R35.00 (>48 km)\nFull bands: R13 (0-8km), R19 (8-14km), R23.50 (14-21km), R26 (21-29km), R30 (29-38km), R32 (38-48km), R35 (>48km).\nConcessions: Students R12.50, Disabled R14.50, Pensioners free off-peak (>65) or 25% off (60-65). Use Connector Card only."
    },
    {
        keywords: ["route", "bus route", "routes", "from to", "where goes", "line", "number route"],
        response: "🛣️ Tshwane Bus Routes: Routes run clockwise from Colbyn (Church Street East) onwards. Key areas include Centurion, Pretoria Central, North, East, West, and Moot.\nView full timetables and routes at www.tshwane.gov.za/?page_id=723 or use the A Re Yeng Mobile App."
    },
    {
        keywords: ["lost property", "lost item", "left something", "found item", "lost and found", "forgot item"],
        response: "🔍 Lost Property: If you've lost an item on a TBS bus, visit Church Square to collect it. Provide details like date, route, and description. Call 012 358 9999 for assistance. Items are held for a limited time."
    },
    {
        keywords: ["concession", "discount", "student", "senior", "pensioner", "disabled", "accessibility", "wheelchair", "scholar"],
        response: "♿ Concessions & Accessibility:\n- Scholars (up to Grade 12, age 19): R12.50 flat fare.\n- Disabled: R14.50 flat fare (any distance).\n- Pensioners 60-65: 25% off off-peak/weekends/holidays; >65: Free off-peak/weekends/holidays.\nBuses are wheelchair accessible (every second bus has a ramp). Apply for concession cards at TBS offices with ID/photo. Renew annually for scholars."
    },
    {
        keywords: ["complaint", "feedback", "issue", "problem", "report fault", "delay", "late bus"],
        response: "📞 Customer Service: For complaints, feedback, delays, or issues, call 012 358 9999 (toll-free: 080 111 1556) or email tbs@tshwane.gov.za. Lodge online via e-Tshwane portal. We're here to help improve your ride!"
    },
    {
        keywords: ["bus stop", "where to board", "pick up", "stop location", "station"],
        response: "🚌 Bus Stops: Catch buses at designated stops across Tshwane (e.g., Church Square, Centurion Mall). Use the timetable at www.tshwane.gov.za/?page_id=723 or the A Re Yeng Mobile App for real-time locations and maps."
    },
    {
        keywords: ["app", "mobile app", "real time", "tracking", "live location", "moving gauteng"],
        response: "📱 Mobile App: Download the A Re Yeng Mobile App or Moving Gauteng App for routes, stops, timetables, and real-time bus tracking. Available on Google Play and App Store. No real-time delays alerts yet, but check the app for updates."
    },
    {
        keywords: ["bus hire", "charter", "rent bus", "group transport", "special hire"],
        response: "🚍 Bus Hire: TBS buses can be hired for groups during off-peak periods. Contact 012 358 9999 for conditions, rates, and availability. Ideal for events or corporate transport."
    },
    {
        keywords: ["connector card", "buy card", "get card", "reload", "top up", "load money"],
        response: "💳 Connector Card: Buy your first card for R30 at TBS offices, Walk-in Centres (e.g., Church Square), or A Re Yeng stations. Reload at stations, ABSA ATMs (chip-enabled), or the app. No cash on buses—use Connector Card only."
    },
    {
        keywords: ["gautrain", "integration", "connect gautrain", "transfer"],
        response: "🔗 Integration with Gautrain: TBS connects to Gautrain stations (e.g., Pretoria Station). Use Connector Card for seamless travel. Wheelchair access available on every second TBS bus and all Gautrain trains. Check Moving Gauteng App for transfers."
    },
    {
        keywords: ["holiday", "public holiday", "sunday", "no service", "festive season"],
        response: "🎉 Public Holidays & Sundays: No service on Sundays and most public holidays. On festive periods (e.g., Dec 24-31, 2024/2025), reduced or no service—check www.tshwane.gov.za for updates. Full service resumes post-holidays."
    },
    {
        keywords: ["emergency", "fire", "medical", "police", "help on bus"],
        response: "🚨 Emergency Services: For emergencies on a bus, contact the driver or call 107 (Tshwane Emergency), 10111 (Police), or 012 358 9999 (TBS). Fire stations by region available at www.tshwane.gov.za."
    },
    {
        keywords: ["timetable", "schedule download", "pdf timetable"],
        response: "📅 Timetables: Download PDFs for Monday-Friday, Saturday, and Sunday routes at www.tshwane.gov.za/?page_id=723. Routes start clockwise from Colbyn. Use A Re Yeng App for mobile access."
    },
    {
        keywords: ["wheelchair", "disabled access", "ramp", "universal access"],
        response: "♿ Accessibility: TBS buses have wheelchair ramps on every second vehicle and designated spaces. Stations have inconsistent universal access—report issues to 012 358 9999. Concession fare: R14.50 flat for disabled."
    },
    {
        keywords: ["claim", "third party", "accident", "injury bus"],
        response: "⚠️ Third Party Claims: For accidents or injuries on TBS, follow the claim process at www.tshwane.gov.za/?page_id=719. Submit forms with details to the Call Centre (012 358 9999)."
    }
    ];

    // FAQs
    const faqs = [
        {
            question: "What are the operating hours of Tshwane Bus Services?",
            answer: "🚌 Our operating hours are:\nMon–Fri: 05:00–19:45\nSat: 05:00–17:00\nSun & Public Holidays: No service."
        },
        {
            question: "How can I pay for my bus ride?",
            answer: "💳 We only accept the *Connector Card* (no cash). You can buy or top it up at Tswane bus offices or on the app."
        },
        {
            question: "How can I contact Tshwane Bus Services?",
            answer: "☎️ Contact Us:\nPhone: +27 12 358 9999\nEmail: tbs@tshwane.gov.za\nWebsite: www.tshwane.gov.za"
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

    // Function to add FAQ messages
    function addFAQMessages() {
        if (!hasAskedQuestion) {
            faqs.forEach((faq, index) => {
                const faqElement = document.createElement("div");
                faqElement.classList.add("faq-message");
                const p = document.createElement("p");
                p.textContent = faq.question;
                p.dataset.faqIndex = index;
                faqElement.appendChild(p);
                chatBox.appendChild(faqElement);
            });
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    }

    // Function to handle FAQ click
    function handleFAQClick(e) {
        e.stopPropagation(); // Prevent outside click event from closing the chat
        const faqIndex = e.target.dataset.faqIndex;
        if (faqIndex !== undefined) {
            // Remove all FAQ messages
            const faqMessages = document.querySelectorAll(".faq-message");
            faqMessages.forEach((msg) => msg.remove());
            // Add the selected FAQ question and answer
            const faq = faqs[faqIndex];
            addMessage(faq.question, "user");
            addMessage(faq.answer, "model");
        }
    }

    // Handle user input
    function handleUserInput() {
        const userInput = inputField.value.trim().toLowerCase();
        if (userInput === "") return;

        // Remove FAQs and mark that user has asked a question
        hasAskedQuestion = true;
        const faqMessages = document.querySelectorAll(".faq-message");
        faqMessages.forEach((msg) => msg.remove());

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

    // Event listeners for chat input
    sendButton.addEventListener("click", handleUserInput);
    inputField.addEventListener("keypress", (e) => {
        if (e.key === "Enter") handleUserInput();
    });

    // Open chat
    chatButton.addEventListener("click", (e) => {
        e.stopPropagation();
        document.body.classList.add("chat-open");
        addFAQMessages(); // Add FAQs if user hasn't asked a question
    });

    // Close chat
    document.querySelector(".chat-window button.close").addEventListener("click", (e) => {
        e.stopPropagation();
        document.body.classList.remove("chat-open");
        // Clear FAQs when closing
        const faqMessages = document.querySelectorAll(".faq-message");
        faqMessages.forEach((msg) => msg.remove());
    });

    // Close chat when clicking outside
    document.addEventListener("click", (e) => {
        if (!chatWindow.contains(e.target) && !chatButton.contains(e.target)) {
            document.body.classList.remove("chat-open");
            // Clear FAQs when closing
            const faqMessages = document.querySelectorAll(".faq-message");
            faqMessages.forEach((msg) => msg.remove());
        }
    });

    // Handle FAQ clicks
    chatBox.addEventListener("click", handleFAQClick);

    // Add FAQs on initial load
    addFAQMessages();
});