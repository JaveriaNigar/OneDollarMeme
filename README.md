# 🚀 OneDollarMeme - AI-Powered Hinglish Meme Generator

![Status Active](https://img.shields.io/badge/Status-Active-brightgreen) ![AI Powered](https://img.shields.io/badge/AI-OpenAI_%2B_FAISS-orange)

**OneDollarMeme** is a fully automated, AI-driven platform that thinks, analyzes, and generates context-aware, culturally relevant **Hinglish** memes on demand. 

Instead of just pasting text on images, this system searches through a massive dataset, understands trends, and creates memes with specific humor styles—all through a sleek web interface.

---

## 🧐 What is this?

Think of OneDollarMeme as your personal, highly intelligent meme artist. You give it a topic, a vibe (like *savage* or *wholesome*), and a tone. 

The system then interacts with an advanced **Python AI Agent**, which:
1. Searches its brain (a gigantic database of 575,000+ memes).
2. Looks up live trends on the internet (Reddit & Twitter).
3. Uses ChatGPT's Vision to "look" at templates.
4. Generates the funniest, most savage Hinglish captions that fit the template perfectly.

All of this happens smoothly behind a beautiful, easy-to-use **Laravel Web Application**.

---

## 💡 Why does this exist?

Creating a good meme requires *cultural context*, *humor matching*, and *visual understanding*. Existing meme generators are dumb—they just slap whatever text you type onto an image. 

OneDollarMeme was built to solve this by:
- **Adding True AI Context**: It actually "understands" what the image format means (e.g., Drake rejecting vs. accepting).
- **Hinglish Focus**: Tailored specifically for the desi audience, capturing the nuance of Hinglish humor.
- **Total Automation**: From retrieving the right blank template using semantic search, to placing the punchline flawlessly.

---

## ⚙️ How it Works (The Magic Workflow)

When you ask for a meme, here is the sequence of events that happens in the background:

### 1. The Request 📥
You log into the web dashboard and enter a topic. (Example: *"Monday mornings but you work from home"*, Style: *Desi*, Tone: *Sarcastic*).

### 2. The Great Search 🔍
The AI Agent generates smart keywords from your prompt and performs a high-speed **Semantic Search** on its local FAISS Vector database (`ImgFlip575K` dataset). If it doesn't find a perfect match locally, it scrapes **Reddit and Twitter** for real-time trending context.

### 3. Vision & Generation 👀
Once the perfect blank template is found, the system uses **OpenAI Vision API** to analyze the visual space. It formulates a highly contextual Hinglish caption, matching the *sarcastic* tone you requested.

### 4. Ranking & Delivery 🏆
The AI doesn't just make one meme; it makes a tiny batch, scores them, and picks the absolute top 3 funniest variants. These are instantly streamed back to your screen, ready to be saved, shared, or posted.
