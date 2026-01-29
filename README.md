# Smart Support Ticket System with AI Agent

## Overview

This project is a backend API prototype for a Helpdesk system where users submit support tickets, and an AI Agent automatically enriches them with:

- Category classification (Technical / Billing / General)
- Sentiment analysis (Positive / Neutral / Negative)
- Urgency level
- Suggested support reply

The AI processing is handled asynchronously using a queue to keep the API responsive and scalable.

---

## Tech Stack

- **PHP**: 8.1+
- **Framework**: Laravel 10
- **Database**: SQLite
- **Queue**: Laravel Queue (sync or database driver)
- **AI Integration**:
    - Mock AI Client (default)

---

## Architecture Overview

The application follows a clean and modular architecture:

- **Controller** – Handles HTTP requests
- **Job** – Offloads AI processing to background execution
- **AI Client Interface** – Abstracts the AI provider
- **AI Client Implementation** – Mock
- **Database** – Stores tickets and AI-enriched data

This design allows easy replacement of the AI provider without changing business logic.

---

## API Endpoints

### Create Ticket

**POST** `/api/tickets`

Request body:
```json
{
  "title": "Payment issue",
  "description": "My payment failed and I am very angry"
}
```

Response:
```json
{
  "message": "Ticket created",
  "ticket_id": 1
}
```

The ticket is saved immediately, while AI analysis is processed asynchronously.

---

### Get Ticket Details

**GET** `/api/tickets/{id}`

Response:
```json
{
  "id": 1,
  "title": "Payment issue",
  "description": "My payment failed and I am very angry",
  "status": "Open",
  "category": "Billing",
  "sentiment": "Negative",
  "urgency": "High",
  "suggested_reply": "Thank you for contacting support. We are reviewing your issue.",
  "created_at": "...",
  "updated_at": "..."
}
```

---

## Setup Instructions (Local)

### 1. Clone the repository
```bash
git clone https://github.com/your-username/smart-support-tickets.git
cd smart-support-tickets
```

### 2. Install dependencies
```bash
composer install
```

### 3. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure database
By default, the project uses SQLite.

```env
DB_CONNECTION=sqlite
```

Create the database file:
```bash
touch database/database.sqlite
```

---

### 5. Run migrations
```bash
php artisan migrate
```

---

### 6. Configure Queue
For simplicity, you can start with the sync driver:

```env
QUEUE_CONNECTION=sync
```

Or use the database queue (recommended):

```bash
php artisan queue:table
php artisan migrate
php artisan queue:work
```

---

### 7. Run the application
```bash
php artisan serve
```

API will be available at:
```
http://127.0.0.1:8000/api
```

---

## AI Integration

### Mock AI

The system uses a **Fake AI Client** that simulates:
- Network delay
- Categorization
- Sentiment analysis
- Suggested reply generation

This allows the application to function exactly as if a real AI service were connected.

---

## Prompt Strategy

The system prompt is designed to strictly control the AI output:

- The AI is instructed to act as a **Helpful Customer Support Agent**
- Output must be **valid JSON only**
- Markdown, explanations, and free text are explicitly forbidden
- A fixed JSON schema is provided in the prompt

This approach ensures:
- Predictable AI responses
- Safe JSON parsing
- No post-processing or cleanup logic required

---

## Testing

The project includes a **Feature Test** that verifies the ticket creation flow.

Run tests:
```bash
php artisan test
```

---

## Future Improvements

- Authentication & authorization
- Ticket status lifecycle (Resolved, Closed)

---
