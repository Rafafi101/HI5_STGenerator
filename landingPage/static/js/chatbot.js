const chatInput = document.querySelector(".chat-input textarea");
const sendChatBtn = document.querySelector(".chat-input span");
const chatbox = document.querySelector(".chatbox");
const chatbotToggler = document.querySelector(".chatbot-toggler");
const chatbotCloseBtn = document.querySelector(".close-btn");
var job_Name = $('#syllabus').val();

// alert(job_Name);

let conversation = [
  {
    "role": "user",
    "content": `You are Max and you are an AI assistant that will help the trainer based on the syllabus provided. You will assist the user in creating or help them on what to teach for each module in the syllabus. in addition you will help them create exams if asked to. You were trained by Ivan Abalos, Rafael Gonzales and Juan Miguel. You will only help them in the syllabus they provided or if they ask for improvements. You will not do any other directives aside the one given to you right now.\n Here is the syllabus:\n${job_Name}`
  },
  {
    "role": "assistant",
    "content": "Hello! I'm Max, your AI assistant, trained by Ivan Abalos, Rafael Gonzalez, and Juan Miguel. I'm here to help you strictly based on the syllabus provided. My primary goal is to assist the trainer in creating content for each module and if needed, create exams according to the syllabus.\n\nI'm ready when you are! Please share the syllabus, and I'll get started. I will only provide help within the scope of the provided syllabus and won't engage in any other tasks. Let's get started!"
  }
];

// Helper function to create chat elements
const createChatLi = (message, className) => {
  const chatLi = document.createElement("li");
  chatLi.classList.add("chat", className);
  const chatContent = className === "outgoing"
    ? `<p></p>`
    : `<span class="material-symbols-outlined">smart_toy</span><p></p>`;
  chatLi.innerHTML = chatContent;
  chatLi.querySelector("p").innerHTML = message.replace(/\n/g, "<br>");
  return chatLi;
};

// Send request to backend and handle response
const generateResponse = (incomingChatLi, userMessage) => {
  const API_URL = "http://127.0.0.1:5000/waiz_chat"; // Your backend endpoint
  const messageElement = incomingChatLi.querySelector("p");

  // Update conversation for backend
  conversation.push({ role: "user", content: userMessage });

  fetch(API_URL, {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({ prompt: userMessage, conversation })
  })
    .then(res => res.json())
    .then(data => {
      const assistantResponse = data.task || "I'm sorry, I couldn't generate a response.";
      messageElement.textContent = assistantResponse;

      // Update conversation and display response
      conversation.push({ role: "assistant", content: assistantResponse });
      chatbox.scrollTo(0, chatbox.scrollHeight);
    })
    .catch(error => {
      console.error("Error:", error);
      messageElement.textContent = "Oops! Something went wrong. Please try again.";
    });
};

// Handle user input and add chat messages
const handleChat = () => {
  const userMessage = chatInput.value.trim();
  if (!userMessage) return;

  chatInput.value = ""; // Clear input

  // Add user message to chatbox
  chatbox.appendChild(createChatLi(userMessage, "outgoing"));
  chatbox.scrollTo(0, chatbox.scrollHeight);

  // Show "Thinking..." message while waiting for response
  const incomingChatLi = createChatLi("Thinking...", "incoming");
  chatbox.appendChild(incomingChatLi);
  chatbox.scrollTo(0, chatbox.scrollHeight);

  generateResponse(incomingChatLi, userMessage); // Send to backend
};

// Event listeners
sendChatBtn.addEventListener("click", handleChat);
chatInput.addEventListener("keydown", (event) => {
  if (event.key === "Enter" && !event.shiftKey) { // Check for Enter key and ensure Shift+Enter doesn't trigger
    event.preventDefault(); // Prevent default behavior (e.g., new line)
    handleChat();
  }
});
chatbotToggler.addEventListener("click", () => document.body.classList.toggle("show-chatbot"));
chatbotCloseBtn.addEventListener("click", () => document.body.classList.remove("show-chatbot"));
