# API Specifications
## MinSU Bongabong Information System

**Document Version:** 1.0  
**Date:** October 4, 2025  
**Full Name:** Mindoro State University - Bongabong Campus Information System  
**Database Engine:** MySQL 8.0+  
**ORM:** Laravel Eloquent
**API Version:** v1  
**Base URL:** `https://api.minsubongabong.edu.ph/v1` *(placeholder domain)*

> **Note:** All domain names (minsubongabong.edu.ph, api.minsubongabong.edu.ph) used in this document are placeholders for demonstration purposes. Actual production domains will be provided by Mindoro State University IT Services.

---

## Overview

This document defines the REST API contracts for the SAS Information System's three modules (SAS, Registrar, USG) and their integration points. All APIs follow RESTful conventions with JSON request/response format.

### Design Principles

- **Resource-Oriented**: URLs represent resources (nouns), not actions (verbs)
- **HTTP Verbs**: GET (read), POST (create), PUT (full update), PATCH (partial update), DELETE (remove)
- **Status Codes**: Meaningful HTTP status codes for success/error states
- **Idempotency**: PUT, PATCH, DELETE operations are idempotent
- **Versioning**: API version in URL path (`/v1/`) for backward compatibility
- **Authentication**: Bearer token (Laravel Sanctum) for authenticated endpoints
- **Pagination**: Cursor-based for large datasets
- **Rate Limiting**: 60 requests/minute per user, 300 requests/minute per IP

---

## Authentication

### Obtain Access Token

**Endpoint:** `POST /auth/login`  
**Description:** Authenticate user and receive access token  
**Authentication:** None (public)

**Request Body:**
```json
{
  "email": "maria.santos@minsubongabong.edu.ph",
  "password": "SecurePass123!",
  "remember": true
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "student_id": "2024-00123",
      "name": "Maria Santos",
      "email": "maria.santos@minsubongabong.edu.ph",
      "role": "student",
      "created_at": "2025-09-01T08:00:00Z"
    },
    "token": "1|laravel_sanctum_token_here",
    "expires_at": "2025-10-18T08:00:00Z"
  },
  "message": "Login successful"
}
```

**Response (401 Unauthorized):**
```json
{
  "success": false,
  "error": {
    "code": "INVALID_CREDENTIALS",
    "message": "Invalid email or password",
    "details": {}
  }
}
```

**Response (429 Too Many Requests - after 5 failed attempts):**
```json
{
  "success": false,
  "error": {
    "code": "ACCOUNT_LOCKED",
    "message": "Account temporarily locked due to multiple failed login attempts",
    "details": {
      "locked_until": "2025-10-04T10:30:00Z",
      "retry_after": 900
    }
  }
}
```

---

### Logout

**Endpoint:** `POST /auth/logout`  
**Description:** Invalidate current access token  
**Authentication:** Required

**Request Headers:**
```
Authorization: Bearer 1|laravel_sanctum_token_here
```

**Response (200 OK):**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

### Get Authenticated User

**Endpoint:** `GET /auth/user`  
**Description:** Retrieve current user profile  
**Authentication:** Required

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "student_id": "2024-00123",
    "name": "Maria Santos",
    "email": "maria.santos@minsubongabong.edu.ph",
    "program": "BS Computer Science",
    "year_level": 3,
    "enrollment_status": "enrolled",
    "role": "student",
    "permissions": ["submit_scholarship_application", "request_documents"],
    "created_at": "2025-09-01T08:00:00Z"
  }
}
```

---

## Module 1: SAS - Scholarship Management API

### List Scholarship Applications (Student View)

**Endpoint:** `GET /sas/scholarships/applications`  
**Description:** Retrieve student's scholarship applications  
**Authentication:** Required (student role)

**Query Parameters:**
- `status` (optional): Filter by status (`pending_review`, `under_review`, `approved`, `rejected`, `pending_documents`)
- `scholarship_type` (optional): Filter by type (`TES`, `TDP`, `institutional`)
- `per_page` (optional): Results per page (default: 20, max: 100)
- `page` (optional): Page number (default: 1)

**Example Request:**
```
GET /sas/scholarships/applications?status=pending_review&per_page=10
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": "sa-2025-0001",
      "scholarship_type": "TES",
      "status": "pending_review",
      "submission_date": "2025-10-01T14:30:00Z",
      "last_updated": "2025-10-01T14:30:00Z",
      "amount": 10000.00,
      "academic_year": "2025-2026",
      "semester": 1,
      "documents": [
        {
          "type": "birth_certificate",
          "filename": "birth_cert.pdf",
          "uploaded_at": "2025-10-01T14:25:00Z",
          "status": "verified"
        },
        {
          "type": "proof_of_income",
          "filename": "income_proof.pdf",
          "uploaded_at": "2025-10-01T14:28:00Z",
          "status": "pending_verification"
        }
      ],
      "timeline": [
        {
          "status": "submitted",
          "timestamp": "2025-10-01T14:30:00Z",
          "actor": "Maria Santos",
          "note": "Application submitted"
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 1,
    "last_page": 1
  }
}
```

---

### Submit Scholarship Application

**Endpoint:** `POST /sas/scholarships/applications`  
**Description:** Submit new scholarship application  
**Authentication:** Required (student role)

**Request Body (multipart/form-data):**
```
scholarship_type: TES
family_income: 150000.00
gpa: 3.8
academic_year: 2025-2026
semester: 1
birth_certificate: [file]
proof_of_income: [file]
latest_grades: [file]
```

**Response (201 Created):**
```json
{
  "success": true,
  "data": {
    "id": "sa-2025-0001",
    "reference_number": "SA-2025-0001",
    "scholarship_type": "TES",
    "status": "pending_review",
    "submission_date": "2025-10-01T14:30:00Z",
    "message": "Application submitted successfully. You will be notified via email once reviewed."
  }
}
```

**Response (422 Unprocessable Entity - validation error):**
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "The given data was invalid",
    "details": {
      "gpa": ["GPA must be at least 2.5 for TES scholarship"],
      "birth_certificate": ["Birth certificate file is required"]
    }
  }
}
```

**Response (409 Conflict - duplicate application):**
```json
{
  "success": false,
  "error": {
    "code": "DUPLICATE_APPLICATION",
    "message": "You have already applied for TES scholarship this semester",
    "details": {
      "existing_application_id": "sa-2025-0001",
      "submitted_at": "2025-10-01T14:30:00Z"
    }
  }
}
```

---

### List All Scholarship Applications (Staff View)

**Endpoint:** `GET /sas/scholarships/applications/all`  
**Description:** Retrieve all scholarship applications for review  
**Authentication:** Required (sas_staff or sas_admin role)

**Query Parameters:**
- `status` (optional): Filter by status
- `scholarship_type` (optional): Filter by type
- `date_from` (optional): Filter by submission date (ISO 8601)
- `date_to` (optional): Filter by submission date (ISO 8601)
- `search` (optional): Search by student name or ID
- `sort` (optional): Sort field (default: `submission_date`)
- `order` (optional): Sort order (`asc`, `desc`) (default: `desc`)
- `per_page` (optional): Results per page
- `page` (optional): Page number

**Example Request:**
```
GET /sas/scholarships/applications/all?status=pending_review&scholarship_type=TES&per_page=50
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": "sa-2025-0001",
      "student": {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "student_id": "2024-00123",
        "name": "Maria Santos",
        "program": "BS Computer Science",
        "year_level": 3,
        "gpa": 3.8
      },
      "scholarship_type": "TES",
      "status": "pending_review",
      "family_income": 150000.00,
      "amount": 10000.00,
      "submission_date": "2025-10-01T14:30:00Z",
      "eligibility": {
        "gpa_met": true,
        "income_threshold_met": true,
        "enrollment_verified": true,
        "documents_complete": false
      },
      "documents_url": "/sas/scholarships/applications/sa-2025-0001/documents"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 50,
    "total": 38,
    "last_page": 1
  },
  "summary": {
    "total_pending": 38,
    "total_under_review": 12,
    "total_approved": 145,
    "total_rejected": 23
  }
}
```

---

### Approve Scholarship Application

**Endpoint:** `PATCH /sas/scholarships/applications/{id}/approve`  
**Description:** Approve a scholarship application  
**Authentication:** Required (sas_staff or sas_admin role)

**Request Body:**
```json
{
  "amount": 10000.00,
  "disbursement_schedule": "monthly",
  "comments": "All requirements met. Approved for full semester grant."
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": "sa-2025-0001",
    "status": "approved",
    "approved_by": "Prof. Elena Cruz",
    "approved_at": "2025-10-04T10:15:00Z",
    "amount": 10000.00,
    "message": "Application approved. Student has been notified via email."
  }
}
```

**Response (403 Forbidden - insufficient permissions):**
```json
{
  "success": false,
  "error": {
    "code": "INSUFFICIENT_PERMISSIONS",
    "message": "You do not have permission to approve scholarship applications",
    "details": {
      "required_role": "sas_staff",
      "your_role": "student"
    }
  }
}
```

---

### Bulk Approve Applications

**Endpoint:** `POST /sas/scholarships/applications/bulk-approve`  
**Description:** Approve multiple applications in single transaction  
**Authentication:** Required (sas_staff or sas_admin role)

**Request Body:**
```json
{
  "application_ids": [
    "sa-2025-0001",
    "sa-2025-0005",
    "sa-2025-0012"
  ],
  "amount": 10000.00,
  "disbursement_schedule": "monthly",
  "comments": "Batch approval for TES Semester 1 AY 2025-2026"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "approved_count": 3,
    "failed_count": 0,
    "results": [
      {
        "id": "sa-2025-0001",
        "status": "approved",
        "message": "Successfully approved"
      },
      {
        "id": "sa-2025-0005",
        "status": "approved",
        "message": "Successfully approved"
      },
      {
        "id": "sa-2025-0012",
        "status": "approved",
        "message": "Successfully approved"
      }
    ]
  },
  "message": "3 applications approved successfully. Students have been notified."
}
```

---

## Module 1: SAS - Organization Management API

### List Organizations

**Endpoint:** `GET /sas/organizations`  
**Description:** Retrieve list of registered organizations  
**Authentication:** Required (any authenticated user)

**Query Parameters:**
- `type` (optional): Filter by type (`minor`, `major`)
- `status` (optional): Filter by status (`active`, `inactive`, `pending_approval`)
- `search` (optional): Search by organization name
- `per_page` (optional): Results per page

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": "org-001",
      "name": "Computer Science Society",
      "type": "major",
      "status": "active",
      "description": "Official organization for CS students...",
      "advisor": {
        "id": "faculty-012",
        "name": "Dr. Juan Dela Cruz",
        "email": "juan.delacruz@minsubongabong.edu.ph"
      },
      "president": {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "name": "James Reyes",
        "student_id": "2023-00456"
      },
      "member_count": 87,
      "registration_date": "2024-08-15T00:00:00Z",
      "logo_url": "https://cdn.minsu.edu.ph/orgs/cs-society-logo.png"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 23,
    "last_page": 2
  }
}
```

---

### Register New Organization

**Endpoint:** `POST /sas/organizations`  
**Description:** Register a new student organization  
**Authentication:** Required (student role)

**Request Body (multipart/form-data):**
```
name: Data Science Club
type: minor
description: Organization focused on data science and machine learning
advisor_id: faculty-015
president_id: 550e8400-e29b-41d4-a716-446655440000
constitution: [PDF file]
```

**Response (201 Created):**
```json
{
  "success": true,
  "data": {
    "id": "org-024",
    "name": "Data Science Club",
    "status": "pending_approval",
    "message": "Registration submitted. Awaiting advisor approval."
  }
}
```

---

### Add Organization Member

**Endpoint:** `POST /sas/organizations/{org_id}/members`  
**Description:** Add member to organization roster  
**Authentication:** Required (organization officer)

**Request Body:**
```json
{
  "student_id": "2024-00789",
  "position": "Secretary",
  "join_date": "2025-10-04"
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "data": {
    "member_id": "mem-456",
    "student": {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "student_id": "2024-00789",
      "name": "Anna Cruz"
    },
    "position": "Secretary",
    "join_date": "2025-10-04",
    "message": "Member added successfully. Notification sent to student."
  }
}
```

---

## Module 1: SAS - Event Calendar API

### List Events

**Endpoint:** `GET /sas/events`  
**Description:** Retrieve campus events  
**Authentication:** Required (any authenticated user)

**Query Parameters:**
- `date_from` (optional): Filter events starting from date (ISO 8601)
- `date_to` (optional): Filter events until date (ISO 8601)
- `category` (optional): Filter by category (`academic`, `social`, `sports`, `cultural`, `workshop`)
- `organizer_id` (optional): Filter by organizer (organization ID or "sas")
- `status` (optional): Filter by status (`published`, `pending_approval`, `cancelled`)
- `per_page` (optional): Results per page

**Example Request:**
```
GET /sas/events?date_from=2025-10-01&date_to=2025-10-31&category=workshop
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": "evt-12345",
      "title": "Introduction to Machine Learning Workshop",
      "description": "Hands-on workshop covering ML fundamentals...",
      "category": "workshop",
      "start_datetime": "2025-10-15T14:00:00Z",
      "end_datetime": "2025-10-15T17:00:00Z",
      "location": "Engineering Building Room 201",
      "organizer": {
        "id": "org-001",
        "name": "Computer Science Society",
        "type": "organization"
      },
      "banner_url": "https://cdn.minsu.edu.ph/events/ml-workshop-banner.jpg",
      "capacity": 50,
      "registered_count": 32,
      "registration_open": true,
      "registration_url": "/sas/events/evt-12345/register",
      "status": "published",
      "created_at": "2025-10-01T10:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 8,
    "last_page": 1
  }
}
```

---

### Create Event

**Endpoint:** `POST /sas/events`  
**Description:** Create new campus event  
**Authentication:** Required (sas_staff or organization officer)

**Request Body (multipart/form-data):**
```
title: Python Programming Bootcamp
description: 3-day intensive Python bootcamp for beginners
category: workshop
start_datetime: 2025-11-01T09:00:00Z
end_datetime: 2025-11-03T17:00:00Z
location: Computer Laboratory 1
capacity: 30
registration_required: true
banner: [image file]
```

**Response (201 Created):**
```json
{
  "success": true,
  "data": {
    "id": "evt-12346",
    "title": "Python Programming Bootcamp",
    "status": "published",
    "created_at": "2025-10-04T11:00:00Z",
    "message": "Event created and synced to USG public calendar"
  }
}
```

**Response (202 Accepted - requires approval):**
```json
{
  "success": true,
  "data": {
    "id": "evt-12346",
    "title": "Python Programming Bootcamp",
    "status": "pending_approval",
    "created_at": "2025-10-04T11:00:00Z",
    "message": "Event submitted for approval. Requires budget approval due to estimated cost ₱10,000."
  }
}
```

---

### Event Created Webhook (Internal Integration)

**Event:** `sas.event.created`  
**Description:** Laravel event fired when SAS creates/publishes event  
**Consumer:** USG Portal Calendar Sync Service

**Event Payload:**
```php
{
  "event_id": "evt-12345",
  "title": "Introduction to Machine Learning Workshop",
  "description": "Hands-on workshop covering ML fundamentals...",
  "category": "workshop",
  "start_datetime": "2025-10-15T14:00:00+08:00",
  "end_datetime": "2025-10-15T17:00:00+08:00",
  "location": "Engineering Building Room 201",
  "organizer": {
    "id": "org-001",
    "name": "Computer Science Society",
    "type": "organization"
  },
  "banner_url": "https://cdn.minsu.edu.ph/events/ml-workshop-banner.jpg",
  "status": "published",
  "created_at": "2025-10-01T10:00:00+08:00"
}
```

**USG Listener Action:**
```php
// Listener: App\Modules\USG\Listeners\SyncEventToPublicCalendar
public function handle(EventCreated $event): void
{
    // Cache event in USG public calendar view
    Cache::tags(['usg', 'calendar'])->put(
        "usg:event:{$event->event_id}",
        $event->toArray(),
        now()->addDays(90)
    );
    
    // Refresh materialized view for public calendar
    DB::statement('REFRESH MATERIALIZED VIEW usg.public_calendar');
}
```

---

## Module 2: Registrar - Document Request API

### Submit Document Request

**Endpoint:** `POST /registrar/requests`  
**Description:** Submit new document request  
**Authentication:** Required (student or alumni role)

**Request Body:**
```json
{
  "document_type": "transcript_of_records",
  "copies": 2,
  "purpose": "for_employment",
  "delivery_method": "digital_download",
  "delivery_address": null
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "data": {
    "id": "req-2025-0123",
    "document_type": "transcript_of_records",
    "copies": 2,
    "amount": 200.00,
    "status": "pending_payment",
    "payment_link": "https://checkout.paymongo.com/pay/xxx",
    "expires_at": "2025-10-05T11:00:00Z",
    "message": "Request created. Please complete payment within 24 hours."
  }
}
```

**Response (403 Forbidden - clearance not met):**
```json
{
  "success": false,
  "error": {
    "code": "CLEARANCE_REQUIRED",
    "message": "Cannot request TOR due to outstanding obligations",
    "details": {
      "outstanding_fees": 2500.00,
      "unreturned_books": 2,
      "action_required": "Clear obligations at Finance Office and Library before requesting TOR"
    }
  }
}
```

---

### Get Payment Status

**Endpoint:** `GET /registrar/requests/{id}/payment`  
**Description:** Check payment status for document request  
**Authentication:** Required (request owner)

**Response (200 OK - payment completed):**
```json
{
  "success": true,
  "data": {
    "request_id": "req-2025-0123",
    "payment_status": "paid",
    "amount": 200.00,
    "payment_method": "gcash",
    "payment_reference": "PAY-abc123xyz",
    "paid_at": "2025-10-04T11:30:00Z",
    "official_receipt_number": "OR-2025-1234",
    "receipt_url": "/registrar/receipts/OR-2025-1234.pdf"
  }
}
```

**Response (200 OK - payment pending):**
```json
{
  "success": true,
  "data": {
    "request_id": "req-2025-0123",
    "payment_status": "pending",
    "amount": 200.00,
    "payment_link": "https://checkout.paymongo.com/pay/xxx",
    "expires_at": "2025-10-05T11:00:00Z",
    "message": "Payment not yet completed. Please complete payment to proceed."
  }
}
```

---

### Payment Webhook (External Integration)

**Endpoint:** `POST /webhooks/paymongo`  
**Description:** Webhook endpoint for Paymongo payment confirmations  
**Authentication:** Signature verification (HMAC)

**Request Headers:**
```
X-Paymongo-Signature: sha256=abc123...
Content-Type: application/json
```

**Request Body (from Paymongo):**
```json
{
  "data": {
    "id": "evt_123abc",
    "type": "event",
    "attributes": {
      "type": "payment.paid",
      "data": {
        "id": "pay_abc123",
        "attributes": {
          "amount": 20000,
          "status": "paid",
          "description": "Document Request: REQ-2025-0123",
          "metadata": {
            "request_id": "req-2025-0123",
            "idempotency_key": "idem-req-2025-0123-1728045600"
          }
        }
      }
    }
  }
}
```

**Response (200 OK):**
```json
{
  "received": true
}
```

**Webhook Processing Logic:**
```php
// Controller: App\Http\Controllers\Webhooks\PaymongoWebhookController

public function handle(Request $request)
{
    // 1. Verify signature
    if (!$this->verifySignature($request)) {
        return response()->json(['error' => 'Invalid signature'], 401);
    }
    
    // 2. Extract payment data
    $paymentId = $request->input('data.attributes.data.id');
    $requestId = $request->input('data.attributes.data.attributes.metadata.request_id');
    $status = $request->input('data.attributes.data.attributes.status');
    
    // 3. Find document request
    $docRequest = DocumentRequest::where('id', $requestId)->first();
    
    if (!$docRequest) {
        Log::warning("Webhook received for unknown request: {$requestId}");
        return response()->json(['received' => true]); // Acknowledge to prevent retries
    }
    
    // 4. Idempotency check
    if ($docRequest->status !== 'pending_payment') {
        Log::info("Webhook already processed for request: {$requestId}");
        return response()->json(['received' => true]);
    }
    
    // 5. Update request status
    DB::transaction(function () use ($docRequest, $paymentId, $status) {
        $docRequest->update([
            'payment_status' => $status,
            'payment_reference' => $paymentId,
            'paid_at' => now(),
            'status' => 'payment_confirmed'
        ]);
        
        // 6. Dispatch document generation job
        GenerateDocument::dispatch($docRequest)
            ->onQueue('documents')
            ->delay(now()->addMinutes(2));
    });
    
    // 7. Send confirmation email
    Mail::to($docRequest->student->email)
        ->send(new PaymentReceivedMail($docRequest));
    
    return response()->json(['received' => true]);
}
```

---

### Get Document Request Status

**Endpoint:** `GET /registrar/requests/{id}`  
**Description:** Retrieve document request details and status  
**Authentication:** Required (request owner or registrar staff)

**Response (200 OK - processing):**
```json
{
  "success": true,
  "data": {
    "id": "req-2025-0123",
    "document_type": "transcript_of_records",
    "copies": 2,
    "status": "processing",
    "payment_status": "paid",
    "amount": 200.00,
    "requested_at": "2025-10-04T10:00:00Z",
    "paid_at": "2025-10-04T11:30:00Z",
    "estimated_completion": "2025-10-05T11:30:00Z",
    "timeline": [
      {
        "status": "pending_payment",
        "timestamp": "2025-10-04T10:00:00Z",
        "completed": true
      },
      {
        "status": "payment_confirmed",
        "timestamp": "2025-10-04T11:30:00Z",
        "completed": true
      },
      {
        "status": "generating_document",
        "timestamp": "2025-10-04T11:32:00Z",
        "completed": false,
        "current": true
      },
      {
        "status": "quality_check",
        "completed": false
      },
      {
        "status": "ready_for_download",
        "completed": false
      }
    ]
  }
}
```

**Response (200 OK - ready):**
```json
{
  "success": true,
  "data": {
    "id": "req-2025-0123",
    "document_type": "transcript_of_records",
    "copies": 2,
    "status": "ready",
    "payment_status": "paid",
    "amount": 200.00,
    "requested_at": "2025-10-04T10:00:00Z",
    "completed_at": "2025-10-04T18:45:00Z",
    "download_url": "https://cdn.minsu.edu.ph/documents/req-2025-0123.pdf?expires=1730678400&signature=abc123",
    "expires_at": "2025-11-04T18:45:00Z",
    "message": "Your document is ready for download. Link expires in 30 days."
  }
}
```

---

### Download Document

**Endpoint:** `GET /registrar/documents/{id}/download`  
**Description:** Generate time-limited signed URL for document download  
**Authentication:** Required (request owner)

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "download_url": "https://cdn.minsu.edu.ph/documents/req-2025-0123.pdf?expires=1728090000&signature=xyz789",
    "filename": "TOR_MariaS_2025-10-04.pdf",
    "expires_at": "2025-10-04T14:00:00Z",
    "message": "Download link valid for 1 hour. You can request a new link if it expires."
  }
}
```

---

## Module 3: USG - Public Portal API

### Get VMGO Content

**Endpoint:** `GET /usg/vmgo`  
**Description:** Retrieve current VMGO content  
**Authentication:** None (public)

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "vision": {
      "content": "<p>To be a leading university in...</p>",
      "last_updated": "2025-09-15T10:00:00Z",
      "updated_by": "James Reyes"
    },
    "mission": {
      "content": "<p>To provide quality education...</p>",
      "last_updated": "2025-09-15T10:00:00Z",
      "updated_by": "James Reyes"
    },
    "goals": {
      "content": "<ul><li>Enhance academic excellence...</li></ul>",
      "last_updated": "2025-09-15T10:00:00Z",
      "updated_by": "James Reyes"
    },
    "objectives": {
      "content": "<ol><li>Increase student retention...</li></ol>",
      "last_updated": "2025-09-15T10:00:00Z",
      "updated_by": "James Reyes"
    }
  }
}
```

---

### List USG Officers

**Endpoint:** `GET /usg/officers`  
**Description:** Retrieve current USG officers  
**Authentication:** None (public)

**Query Parameters:**
- `department` (optional): Filter by department (`executive`, `legislative`, `administrative`)
- `term` (optional): Filter by term (default: current term)

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "current_term": {
      "start_date": "2025-07-01",
      "end_date": "2026-06-30",
      "officers": [
        {
          "id": "officer-001",
          "name": "James Reyes",
          "position": "President",
          "department": "executive",
          "photo_url": "https://cdn.minsu.edu.ph/officers/james-reyes.jpg",
          "email": "usg.president@minsubongabong.edu.ph",
          "office_hours": "Mon-Fri 2:00 PM - 4:00 PM",
          "term_start": "2025-07-01",
          "term_end": "2026-06-30"
        }
      ]
    }
  }
}
```

---

### List Public Events (USG Calendar)

**Endpoint:** `GET /usg/calendar`  
**Description:** Retrieve public campus events (synced from SAS)  
**Authentication:** None (public)

**Query Parameters:**
- `month` (optional): Filter by month (YYYY-MM format)
- `category` (optional): Filter by category
- `search` (optional): Search event titles

**Example Request:**
```
GET /usg/calendar?month=2025-10&category=workshop
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": "evt-12345",
      "title": "Introduction to Machine Learning Workshop",
      "description": "Hands-on workshop covering ML fundamentals...",
      "category": "workshop",
      "start_datetime": "2025-10-15T14:00:00+08:00",
      "end_datetime": "2025-10-15T17:00:00+08:00",
      "location": "Engineering Building Room 201",
      "organizer_name": "Computer Science Society",
      "banner_url": "https://cdn.minsu.edu.ph/events/ml-workshop-banner.jpg",
      "registration_open": true,
      "source": "sas",
      "synced_at": "2025-10-01T10:05:00+08:00"
    }
  ],
  "meta": {
    "total": 8,
    "month": "2025-10"
  }
}
```

**Data Flow:**
```
SAS Event Creation → Laravel Event `EventCreated` → USG Listener → Cache/DB Update → Public API
```

**Sync Latency:** <5 minutes (event-driven)

---

### Search Resolutions

**Endpoint:** `GET /usg/resolutions`  
**Description:** Search and filter USG resolutions  
**Authentication:** None (public, except restricted resolutions)

**Query Parameters:**
- `search` (optional): Full-text search (title, summary, PDF content)
- `category` (optional): Filter by category
- `year` (optional): Filter by year (e.g., 2025)
- `status` (optional): Filter by status (`pending`, `approved`, `implemented`, `rejected`)
- `per_page` (optional): Results per page

**Example Request:**
```
GET /usg/resolutions?search=scholarship&year=2025&status=approved
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": "res-2025-015",
      "resolution_number": "2025-015",
      "title": "Resolution Increasing Scholarship Allocation",
      "category": "financial",
      "date_filed": "2025-09-20",
      "status": "approved",
      "summary": "Resolution to increase scholarship budget allocation from ₱500,000 to ₱750,000 for AY 2025-2026.",
      "pdf_url": "/usg/resolutions/2025-015/view",
      "download_url": "/usg/resolutions/2025-015/download",
      "visibility": "public"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 3,
    "last_page": 1
  }
}
```

---

### List Announcements

**Endpoint:** `GET /usg/announcements`  
**Description:** Retrieve public announcements  
**Authentication:** None (public)

**Query Parameters:**
- `priority` (optional): Filter by priority (`normal`, `urgent`, `critical`)
- `category` (optional): Filter by category
- `active_only` (optional): Show only non-expired (default: true)

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": "ann-2025-034",
      "title": "Scholarship Application Deadline Extended",
      "content": "<p>The deadline for TES scholarship applications has been extended to October 31, 2025...</p>",
      "priority": "urgent",
      "category": "deadlines",
      "published_at": "2025-10-03T08:00:00+08:00",
      "expires_at": "2025-10-31T23:59:59+08:00",
      "featured": true,
      "banner_url": "https://cdn.minsu.edu.ph/announcements/scholarship-deadline.jpg"
    }
  ],
  "meta": {
    "total": 5
  }
}
```

---

## Common Response Formats

### Success Response Structure
```json
{
  "success": true,
  "data": { /* Response data */ },
  "meta": { /* Pagination, counts, etc. */ },
  "message": "Optional success message"
}
```

### Error Response Structure
```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Human-readable error message",
    "details": { /* Additional error context */ }
  }
}
```

### HTTP Status Codes

| Code | Meaning | Use Case |
|------|---------|----------|
| 200 | OK | Successful GET, PATCH, DELETE |
| 201 | Created | Successful POST (resource created) |
| 202 | Accepted | Request accepted for async processing |
| 400 | Bad Request | Malformed request syntax |
| 401 | Unauthorized | Missing or invalid authentication |
| 403 | Forbidden | Authenticated but insufficient permissions |
| 404 | Not Found | Resource doesn't exist |
| 409 | Conflict | Duplicate resource or constraint violation |
| 422 | Unprocessable Entity | Validation errors |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server-side error |
| 503 | Service Unavailable | Temporary service disruption |

---

## Rate Limiting

**Per-User Limits:**
- Authenticated users: 60 requests/minute
- Public endpoints: 300 requests/minute per IP

**Headers:**
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1728045600
```

**Rate Limit Exceeded Response (429):**
```json
{
  "success": false,
  "error": {
    "code": "RATE_LIMIT_EXCEEDED",
    "message": "Too many requests. Please try again later.",
    "details": {
      "retry_after": 30,
      "limit": 60,
      "window": "1 minute"
    }
  }
}
```

---

## Pagination

**Cursor-Based Pagination (preferred for large datasets):**
```
GET /sas/events?cursor=eyJpZCI6MTIzfQ==&per_page=20
```

**Response:**
```json
{
  "success": true,
  "data": [ /* 20 events */ ],
  "meta": {
    "next_cursor": "eyJpZCI6MTQzfQ==",
    "prev_cursor": "eyJpZCI6MTAzfQ==",
    "per_page": 20
  }
}
```

**Offset-Based Pagination (simpler, for smaller datasets):**
```
GET /sas/scholarships/applications?page=2&per_page=50
```

**Response:**
```json
{
  "data": [ /* 50 applications */ ],
  "meta": {
    "current_page": 2,
    "per_page": 50,
    "total": 237,
    "last_page": 5
  }
}
```

---

## Authentication Flow

```
1. User → POST /auth/login (email + password)
2. Server → Validate credentials
3. Server → Generate Sanctum token
4. Server → Return token + user data
5. Client → Store token (localStorage or httpOnly cookie)
6. Client → Include token in all subsequent requests (Authorization: Bearer {token})
7. Server → Validate token on each request
8. User → POST /auth/logout (invalidate token)
```

---

## Integration Testing Examples

### cURL Example: Login
```bash
curl -X POST https://api.minsubongabong.edu.ph/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "maria.santos@minsubongabong.edu.ph",
    "password": "SecurePass123!"
  }'
```

### cURL Example: Submit Scholarship Application
```bash
curl -X POST https://api.minsubongabong.edu.ph/v1/sas/scholarships/applications \
  -H "Authorization: Bearer 1|laravel_sanctum_token" \
  -F "scholarship_type=TES" \
  -F "family_income=150000" \
  -F "gpa=3.8" \
  -F "birth_certificate=@/path/to/birth_cert.pdf"
```

### JavaScript Example: Fetch Events
```javascript
fetch('https://api.minsubongabong.edu.ph/v1/usg/calendar?month=2025-10', {
  method: 'GET',
  headers: {
    'Content-Type': 'application/json'
  }
})
.then(response => response.json())
.then(data => console.log(data.data))
.catch(error => console.error('Error:', error));
```

---

## API Versioning Strategy

**Current Version:** v1  
**Deprecation Policy:** Minimum 6 months notice before removing deprecated endpoints

**Version Migration Path:**
- v1 → v2: Major breaking changes (new response structures, removed endpoints)
- v1.1 → v1.2: Additive changes only (new fields, new endpoints, backward compatible)

**Deprecation Header:**
```
Deprecation: true
Sunset: Sat, 01 Jul 2026 00:00:00 GMT
Link: <https://docs.minsubongabong.edu.ph/api/v2>; rel="successor-version"
```

---

## Webhook Security

**Signature Verification (HMAC SHA-256):**

```php
function verifyWebhookSignature(Request $request): bool
{
    $signature = $request->header('X-Paymongo-Signature');
    $secret = config('services.paymongo.webhook_secret');
    $payload = $request->getContent();
    
    $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    
    return hash_equals($expectedSignature, $signature);
}
```

**Idempotency:**
- All webhooks processed with idempotency check
- Duplicate webhook events return 200 OK without processing
- Audit log tracks all webhook attempts (success, failure, duplicate)

---

## API Documentation Tools

**Recommended Tools:**
- **Postman Collection**: Importable collection with all endpoints and example requests
- **OpenAPI/Swagger Spec**: Machine-readable API specification (generated from Laravel routes)
- **Scalar API Documentation**: Interactive documentation UI (cleaner than Swagger UI)

**Auto-Generated Documentation:**
```php
// Install: composer require knuckleswtf/scribe
php artisan scribe:generate
```

Generates documentation at `/docs/api` with interactive request builder.

---

## Monitoring & Analytics

**Recommended Tracking:**
- Response time percentiles (p50, p95, p99)
- Error rate by endpoint
- Most frequently used endpoints
- Failed authentication attempts
- Webhook delivery success rate

**Tools:**
- Laravel Telescope (development)
- Sentry (error tracking)
- Custom database logging for business metrics

---

**Document Maintenance:**
- Update API specs when new endpoints added
- Version changes documented in changelog
- Breaking changes require major version bump
- Deprecation notices added 6 months before removal

