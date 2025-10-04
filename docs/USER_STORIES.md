# User Stories & Acceptance Criteria
## MinSU Bongabong Information System

**Document Version:** 1.0  
**Date:** October 4, 2025  
**Full Name:** Mindoro State University - Bongabong Campus Information System  
**Format:** User Story → Acceptance Criteria (Given-When-Then)

---

## Story Prioritization Framework

- **P0 (Must Have)**: Core functionality blocking launch
- **P1 (Should Have)**: Important but system functional without
- **P2 (Nice to Have)**: Enhancement for future versions

---

## Module 1: Student Affairs (SAS) - Scholarship Management

### Epic: Scholarship Application & Approval Workflow

---

#### Story SAS-001: Submit Scholarship Application Online
**Priority:** P0  
**Persona:** Maria Santos (Student)  
**Story:**  
As a **student**, I want to **submit my scholarship application online with required documents** so that **I don't have to travel to campus and risk missing the deadline**.

**Acceptance Criteria:**

**Given** I am a logged-in student on the SAS scholarship portal  
**When** I navigate to "Apply for Scholarship"  
**Then** I should see a form with fields for:
- Scholarship type selection (TES, TDP, Institutional)
- Personal information pre-filled from my profile
- Family income information
- Upload zones for required documents (Birth Certificate, Grades, Proof of Income)
- Terms and conditions checkbox
- Submit button

**Given** I have filled all required fields and uploaded all documents  
**When** I click "Submit Application"  
**Then** the system should:
- Validate file types (PDF, JPG, PNG only) and sizes (<5MB each)
- Display success message "Application submitted successfully. Reference number: SA-2025-0001"
- Send confirmation email with application reference number
- Show application status as "Pending Review" in my dashboard

**Given** I attempt to submit with missing required fields  
**When** I click "Submit Application"  
**Then** the system should:
- Prevent submission
- Highlight missing fields in red
- Display error message "Please complete all required fields and upload documents"

**Given** I submit an application for a scholarship type I've already applied for this semester  
**When** I click "Submit Application"  
**Then** the system should:
- Prevent duplicate submission
- Display error message "You have already applied for TES this semester. Reference: SA-2025-0001"

---

#### Story SAS-002: Review and Validate Scholarship Applications
**Priority:** P0  
**Persona:** Prof. Elena Cruz (SAS Staff)  
**Story:**  
As **SAS staff**, I want to **review pending scholarship applications with filtering and bulk actions** so that **I can process 50+ applications efficiently without manual paperwork**.

**Acceptance Criteria:**

**Given** I am logged in as SAS staff  
**When** I navigate to "Scholarship Management" dashboard  
**Then** I should see:
- Summary cards showing: Total pending (50), Under review (12), Approved (38), Rejected (5)
- Table of applications with columns: Reference, Student Name, Type, Submitted Date, Status, Actions
- Filter dropdowns for: Scholarship Type, Status, Submission Date Range
- Search bar for student name or reference number
- Bulk action checkboxes and "Bulk Approve" / "Bulk Reject" buttons

**Given** I am viewing an application in the table  
**When** I click on the student name or reference number  
**Then** I should see:
- Full application details (student info, family income, GPA)
- Uploaded documents with inline preview (PDF viewer, image gallery)
- Eligibility checklist auto-populated (GPA ≥85%, Enrolled status: Yes, Income threshold: Met)
- Comments section for internal notes
- Action buttons: "Approve", "Request More Info", "Reject"

**Given** I have reviewed an application and it meets all criteria  
**When** I click "Approve"  
**Then** the system should:
- Change status to "Approved"
- Log my user ID and timestamp as approver
- Send email notification to student: "Your TES scholarship application (SA-2025-0001) has been approved. Disbursement details will follow."
- Move application out of "Pending" queue

**Given** I select 10 applications via checkboxes that all meet criteria  
**When** I click "Bulk Approve"  
**Then** the system should:
- Show confirmation modal: "Approve 10 applications?"
- Upon confirmation, approve all selected applications in single transaction
- Send individual email notifications to all 10 students
- Update dashboard counts immediately

**Given** an application is missing required documents  
**When** I click "Request More Info"  
**Then** the system should:
- Open modal with pre-populated email template
- Allow me to specify which documents are needed
- Send email to student with 7-day deadline
- Set application status to "Pending Additional Documents"
- Auto-reject if student doesn't respond within 7 days

---

#### Story SAS-003: Track My Scholarship Application Status
**Priority:** P0  
**Persona:** Maria Santos (Student)  
**Story:**  
As a **student**, I want to **see real-time status of my scholarship application** so that **I know whether it's been reviewed and if action is needed**.

**Acceptance Criteria:**

**Given** I am logged in as a student with submitted applications  
**When** I navigate to "My Scholarships" dashboard  
**Then** I should see:
- Card for each application showing: Reference number, Type, Submission date, Current status
- Status badge color-coded: Yellow (Pending Review), Blue (Under Review), Green (Approved), Red (Rejected), Orange (Pending Docs)
- Timeline visualization showing: Submitted → Under Review → Approved (with dates)

**Given** my application status is "Pending Additional Documents"  
**When** I view the application details  
**Then** I should see:
- Red alert banner: "Action Required: Please upload additional documents by Oct 15, 2025"
- List of requested documents with upload buttons
- Days remaining until deadline (7 days)
- "Submit Additional Documents" button

**Given** I upload the requested documents  
**When** I click "Submit Additional Documents"  
**Then** the system should:
- Validate uploads (same file type/size rules)
- Change status back to "Pending Review"
- Send email notification to SAS staff that additional docs were provided
- Display success message: "Documents submitted successfully. Your application is back in review queue."

**Given** my application is approved  
**When** I view the application  
**Then** I should see:
- Green success banner: "Congratulations! Your TES scholarship has been approved."
- Approval details: Approved by (Prof. Elena Cruz), Approved date (Oct 10, 2025), Amount (₱10,000/semester)
- Next steps: "Disbursement will be processed within 5 business days. Check your email for payment details."

---

### Epic: Organization Registry & Management

---

#### Story SAS-004: Register Student Organization
**Priority:** P0  
**Persona:** James Reyes (Organization Officer)  
**Story:**  
As a **student organization officer**, I want to **register my organization online with required documents** so that **we are officially recognized and can access university resources**.

**Acceptance Criteria:**

**Given** I am logged in as a student  
**When** I navigate to "Register New Organization"  
**Then** I should see a form with:
- Organization name field
- Organization type radio buttons (Minor, Major)
- Description textarea (mission, goals)
- Faculty advisor dropdown (list of faculty members)
- President/officer information fields
- Constitution document upload (<10MB PDF)
- Submit button

**Given** I fill all fields and upload a valid constitution  
**When** I click "Submit Registration"  
**Then** the system should:
- Validate advisor is not already advising 3+ organizations (system limit)
- Create organization profile with status "Pending Approval"
- Send email to selected faculty advisor requesting acceptance
- Send confirmation email to me with registration reference
- Display success message: "Registration submitted. Awaiting advisor approval."

**Given** the faculty advisor receives the email  
**When** they click "Accept Advisor Role" link  
**Then** the system should:
- Change organization status to "Active"
- Grant me and other officers access to organization dashboard
- Send confirmation email: "Your organization [Name] is now active"

**Given** I attempt to register an organization with a name that already exists  
**When** I click "Submit Registration"  
**Then** the system should:
- Prevent submission
- Display error: "Organization name '[Name]' already exists. If this is your organization, contact SAS office."

---

#### Story SAS-005: Manage Organization Members
**Priority:** P0  
**Persona:** James Reyes (Organization Officer)  
**Story:**  
As an **organization officer**, I want to **add and remove members with position assignments** so that **we maintain accurate membership records required by SAS**.

**Acceptance Criteria:**

**Given** I am logged in as an officer of an active organization  
**When** I navigate to my organization dashboard and click "Members" tab  
**Then** I should see:
- Table of current members: Name, Student ID, Position, Join Date, Actions
- "Add Member" button
- "Export Roster" button (downloads CSV)
- Total member count displayed

**Given** I click "Add Member"  
**When** the modal opens  
**Then** I should see:
- Student search field (search by name or student ID)
- Position dropdown (President, Vice President, Secretary, Treasurer, Member, etc.)
- Join date picker (defaults to today)
- "Add" and "Cancel" buttons

**Given** I search for a student and they appear in results  
**When** I select them, choose position "Secretary", and click "Add"  
**Then** the system should:
- Validate student is currently enrolled
- Add student to member roster
- Update total member count
- Display success message: "[Student Name] added as Secretary"
- Send email notification to student: "You've been added to [Org Name] as Secretary"

**Given** I want to remove a member  
**When** I click "Remove" action button for that member  
**Then** the system should:
- Show confirmation modal: "Remove [Student Name] from organization?"
- Upon confirmation, soft-delete member (keeps historical record)
- Update member count
- Send email to removed student notifying them of removal

**Given** an organization has only 1 officer (the president)  
**When** the president attempts to remove themselves  
**Then** the system should:
- Prevent action
- Display error: "Cannot remove yourself as the only officer. Assign another president first or deactivate organization."

---

#### Story SAS-006: Submit Organization Activity Reports
**Priority:** P1  
**Persona:** James Reyes (Organization Officer)  
**Story:**  
As an **organization officer**, I want to **submit quarterly activity reports online** so that **we meet SAS compliance requirements and avoid deactivation**.

**Acceptance Criteria:**

**Given** I am logged in as an organization officer  
**When** I navigate to "Reports" tab  
**Then** I should see:
- List of report periods: Q1 (Jul-Sep), Q2 (Oct-Dec), Q3 (Jan-Mar), Q4 (Apr-Jun)
- Status for each: Not Started, Overdue (red), Submitted (green)
- "Submit Report" button for pending quarters
- Past submitted reports with "View" links

**Given** I click "Submit Report" for Q1  
**When** the form opens  
**Then** I should see fields for:
- Activities conducted (list with date, title, description, attendance)
- Budget utilization (if applicable)
- Challenges faced (textarea)
- Plans for next quarter (textarea)
- Supporting documents upload (photos, certificates, attendance sheets)
- Submit button

**Given** I fill all required fields  
**When** I click "Submit Report"  
**Then** the system should:
- Validate at least 1 activity is listed
- Mark quarter as "Submitted" with green status
- Send confirmation email to advisor (cc to SAS staff)
- Display success message: "Q1 report submitted successfully. Thank you for staying compliant!"

**Given** the report deadline has passed and I haven't submitted  
**When** I log in  
**Then** I should see:
- Red banner alert: "Overdue Report: Q1 activity report was due Oct 15. Submit now to avoid deactivation."
- Email reminder sent 7 days before deadline, 1 day after deadline, and 7 days after deadline

**Given** 2 consecutive quarters are overdue  
**When** the system runs nightly compliance check  
**Then** it should:
- Automatically change organization status to "Inactive - Non-Compliant"
- Send email to officers and advisor: "Your organization has been deactivated due to missing activity reports. Contact SAS to reactivate."
- Prevent organization from creating new events or accessing resources

---

### Epic: Event Calendar Management

---

#### Story SAS-007: Create Campus Event
**Priority:** P0  
**Persona:** Prof. Elena Cruz (SAS Staff) / James Reyes (Organization Officer)  
**Story:**  
As **SAS staff or organization officer**, I want to **create campus events that automatically appear on the public calendar** so that **all students can discover and attend our activities**.

**Acceptance Criteria:**

**Given** I am logged in as SAS staff or organization officer  
**When** I navigate to "Create Event"  
**Then** I should see a form with:
- Event title (required, max 100 chars)
- Description (rich text editor, max 2000 chars)
- Date and time pickers (start and end)
- Location field (with campus venue autocomplete)
- Category dropdown (Academic, Social, Sports, Cultural, Workshop, etc.)
- Organizer auto-filled (my organization or "SAS Office")
- Venue capacity field (optional, for registration limits)
- Banner image upload (optional, 1200x630px recommended)
- Registration required checkbox (if yes, adds RSVP feature)
- Submit button

**Given** I fill all required fields  
**When** I click "Submit"  
**Then** the system should:
- Validate date is in the future
- Check for venue conflicts (same location, overlapping time)
- If no conflict: Create event with status "Published"
- If conflict: Show warning "Venue '[Name]' is already booked 2-4 PM by [Event]. Choose different venue or time."
- Fire Laravel event `EventCreated` to sync with USG portal
- Display success message: "Event created! It will appear on public calendar within 5 minutes."

**Given** I create an event requiring university resources (budget >₱5000 or venue with capacity >200)  
**When** I click "Submit"  
**Then** the system should:
- Set event status to "Pending Approval"
- Send email to SAS director for approval
- Display message: "Event submitted for approval. You'll be notified within 2 business days."
- Not publish to public calendar until approved

**Given** my event is approved by SAS director  
**When** they click "Approve" in their dashboard  
**Then** the system should:
- Change status to "Published"
- Sync to USG public calendar
- Send approval email to me
- Send optional event reminder email to all students 1 day before event

**Given** an event is published on SAS calendar  
**When** the USG portal event sync listener runs  
**Then** the event should:
- Appear on USG public calendar within 5 minutes (Laravel event driven)
- Display with same title, date, time, location, organizer
- Link back to detailed view on SAS calendar

---

#### Story SAS-008: View and Filter Campus Events
**Priority:** P0  
**Persona:** Maria Santos (Student)  
**Story:**  
As a **student**, I want to **view upcoming campus events with filtering options** so that **I can discover activities relevant to my interests**.

**Acceptance Criteria:**

**Given** I visit the USG public calendar (no login required)  
**When** the page loads  
**Then** I should see:
- Calendar view with current month displayed
- Events shown as colored dots/blocks on corresponding dates
- Category legend (color coding by event type)
- View toggle buttons: Month, Week, List
- Filter dropdowns: Category (All, Academic, Social, Sports, etc.), Organizer (All, SAS, USG, Org Name)
- Search bar for event title keyword search

**Given** I click on an event in the calendar  
**When** the event modal opens  
**Then** I should see:
- Event banner image (if provided)
- Full title and description
- Date, time, location with map link (if applicable)
- Organizer name with logo
- Registration button (if registration enabled)
- "Add to Calendar" button (generates .ics file for export)
- Share buttons (Facebook, Twitter, copy link)

**Given** I filter by "Category: Workshops"  
**When** the filter applies  
**Then** the calendar should:
- Show only workshop events
- Update URL parameter ?category=workshops (shareable link)
- Maintain filter when switching between Month/Week/List views

**Given** I switch to "List View"  
**When** the view changes  
**Then** I should see:
- Events sorted chronologically (nearest first)
- Each event card showing: Title, Date/Time, Location, Organizer, Category badge
- "Load More" pagination (shows 20 events at a time)
- Same filters remain functional

**Given** I click "Add to Calendar" on an event  
**When** the download triggers  
**Then** the system should:
- Generate .ics file with event details
- Include event title, description, location, start/end time in user's timezone
- File should be importable to Google Calendar, Apple Calendar, Outlook

---

## Module 2: Registrar - Document Request System

### Epic: Student Document Request Workflow

---

#### Story REG-001: Request Academic Document
**Priority:** P0  
**Persona:** Maria Santos (Student)  
**Story:**  
As a **student**, I want to **request academic documents online with immediate payment options** so that **I don't have to take time off work to visit campus**.

**Acceptance Criteria:**

**Given** I am logged in as a student  
**When** I navigate to "Request Document"  
**Then** I should see:
- Document type dropdown with options: Certificate of Grades (COG), Certificate of Enrollment (COE), Transcript of Records (TOR), Certificate of Authentication & Verification (CAV)
- Document type selection shows: Description, Processing time, Price
- Purpose dropdown (For employment, For scholarship, For transfer, For abroad processing, Others)
- Number of copies field (default 1, max 10)
- Delivery method radio buttons: Digital Download (Free), Pickup (Free), Courier (₱150)
- Total price calculation displayed dynamically
- "Proceed to Payment" button

**Given** I select "Transcript of Records" and 2 copies  
**When** the price updates  
**Then** I should see:
- Base price: ₱100 × 2 = ₱200
- Delivery fee: ₱0 (Digital Download selected)
- Total: ₱200

**Given** I click "Proceed to Payment"  
**When** the payment page loads  
**Then** I should:
- See order summary: Document type, Copies, Delivery, Total
- See payment method options: GCash, PayMaya, Credit/Debit Card, Bank Transfer
- See payment expiration timer: "Complete payment within 24 hours"
- Be able to click payment method to proceed

**Given** I select "GCash" as payment method  
**When** I click "Pay with GCash"  
**Then** the system should:
- Generate payment intent with Paymongo API
- Include idempotency key (prevents duplicate if I click twice)
- Redirect me to Paymongo checkout page with GCash QR code
- Store request status as "Pending Payment"

**Given** I complete payment on Paymongo  
**When** payment succeeds  
**Then** Paymongo should:
- Send webhook to our server with payment confirmation
- Our server validates webhook signature
- Update request status to "Payment Confirmed"
- Dispatch background job `GenerateDocument` to queue
- Send email: "Payment received! Your TOR is being generated and will be ready within 24 hours."
- Redirect user back to our site showing "Payment Successful" message

---

#### Story REG-002: Track Document Request Status
**Priority:** P0  
**Persona:** Maria Santos (Student)  
**Story:**  
As a **student**, I want to **see real-time status of my document request** so that **I know when it's ready for download or pickup**.

**Acceptance Criteria:**

**Given** I am logged in as a student  
**When** I navigate to "My Requests"  
**Then** I should see:
- Table of all my requests: Request ID, Document Type, Copies, Request Date, Status, Actions
- Status badge color-coded: Gray (Pending Payment), Blue (Processing), Green (Ready), Yellow (Downloaded/Picked Up)
- Filter dropdown: All, Pending Payment, Processing, Ready
- Search bar for request ID

**Given** I have a request with status "Processing"  
**When** I click on the request ID  
**Then** I should see:
- Progress timeline: Payment Confirmed ✓ → Generating Document (current) → Quality Check → Ready for Download
- Estimated completion time: "Within 24 hours" or specific time
- Request details: Document type, Copies, Purpose, Delivery method, Amount paid
- Payment receipt download link

**Given** my document generation job completes successfully  
**When** the system updates the request  
**Then** it should:
- Change status to "Ready"
- Send email notification: "Your TOR is ready for download! Access it here: [link]"
- Send SMS notification if email not opened within 48 hours
- Update timeline showing "Ready for Download" step complete

**Given** my request status is "Ready" and delivery is "Digital Download"  
**When** I view the request  
**Then** I should see:
- Green success alert: "Your document is ready!"
- "Download Document" button (large, prominent)
- Document expiration notice: "Download expires in 30 days (Jan 15, 2026)"
- Option to request reissue if link expires

**Given** I click "Download Document"  
**When** the download starts  
**Then** the system should:
- Generate secure, time-limited signed URL to PDF in cloud storage
- Track download timestamp (first download triggers status change to "Downloaded")
- Display watermark on PDF: "Official Digital Copy - Issued to [Student Name] on [Date]"
- Allow multiple downloads within 30-day window (same link remains valid)

**Given** 30 days have passed since document was ready  
**When** I attempt to download  
**Then** the system should:
- Show expired message: "Download link has expired. Request reissue for ₱50 (processing fee)."
- Provide "Request Reissue" button that creates new request with discounted price

---

#### Story REG-003: Process Document Requests (Staff)
**Priority:** P0  
**Persona:** Registrar Staff  
**Story:**  
As **registrar staff**, I want to **view all pending requests and manually intervene only when needed** so that **most documents process automatically but I can handle exceptions**.

**Acceptance Criteria:**

**Given** I am logged in as registrar staff  
**When** I navigate to "Document Requests Dashboard"  
**Then** I should see:
- Summary cards: Pending Payment (12), Processing (8), Quality Check (3), Ready (45), Completed (230)
- Table of all requests with filters: Status, Document Type, Date Range, Student Name/ID
- "Auto-Process" toggle (enabled by default) showing system handles COG/COE automatically
- Alerts section flagging exceptions (e.g., "TOR for student with incomplete grades")

**Given** a COG request with status "Processing"  
**When** the background job `GenerateDocument` runs  
**Then** it should:
- Query student grades from database
- Load PDF template for COG
- Populate template with: Student name, ID, Program, Grades for last semester
- Apply digital signature and university seal watermark
- Upload PDF to Cloudinary with secure storage
- Update request status to "Quality Check"
- If successful: Auto-approve and change to "Ready"
- If error (e.g., missing grades): Flag for manual review and send staff alert

**Given** a TOR request requires manual review (flagged exception)  
**When** I click on the request  
**Then** I should see:
- Alert banner: "Requires Manual Review: Student has incomplete grade for COSC 101"
- Student's full academic record displayed
- Option to: "Generate with placeholder (mark incomplete)", "Contact student for clarification", "Cancel request and refund"
- Comment field for internal notes
- "Approve and Generate" or "Cancel" buttons

**Given** I resolve the issue and click "Approve and Generate"  
**When** I submit  
**Then** the system should:
- Retry document generation with manual override flag
- Generate TOR with noted incomplete course
- Send email to student: "Your TOR is ready. Note: COSC 101 marked incomplete pending grade submission."
- Change status to "Ready"

**Given** the "Auto-Process" toggle is disabled  
**When** new paid requests come in  
**Then** the system should:
- Generate documents but hold in "Quality Check" status (don't auto-release)
- Require staff to manually review and approve each document before status changes to "Ready"
- Send daily digest email to staff: "15 documents awaiting quality check approval"

---

### Epic: Payment Processing & Reconciliation

---

#### Story REG-004: Handle Payment Webhooks Reliably
**Priority:** P0  
**Persona:** System (Backend Service)  
**Story:**  
As the **payment webhook handler**, I want to **process Paymongo payment confirmations with retry logic** so that **no paid document request is lost due to temporary failures**.

**Acceptance Criteria:**

**Given** Paymongo sends a payment success webhook  
**When** our webhook endpoint receives the POST request  
**Then** the system should:
- Validate webhook signature using Paymongo secret key
- Extract payment intent ID and status from payload
- Find corresponding document request using idempotency key
- If request found and status is "Pending Payment": Update to "Payment Confirmed"
- If request already processed: Return 200 OK (idempotent - no duplicate processing)
- If signature invalid: Return 401 Unauthorized and log security alert

**Given** webhook processing fails due to database timeout  
**When** the error occurs  
**Then** the system should:
- Return 500 Server Error to Paymongo
- Paymongo automatically retries webhook (3 attempts: immediate, 5 min, 30 min)
- On retry, successfully process webhook
- Log all attempts for audit trail

**Given** all 3 webhook retry attempts fail  
**When** the system detects failed webhook  
**Then** the system should:
- Create alert in admin dashboard: "Failed Webhook: Payment ID [xxx] for Request [REQ-001]"
- Send email to registrar staff with manual reconciliation instructions
- Provide "Manual Confirm Payment" button in admin panel that staff can click after verifying payment in Paymongo dashboard

**Given** a duplicate webhook is received (same payment ID)  
**When** processing begins  
**Then** the system should:
- Check if payment already processed (database lookup)
- Skip redundant processing
- Return 200 OK (acknowledge receipt without action)
- Log as "Duplicate webhook ignored"

---

#### Story REG-005: Reconcile Payments Daily
**Priority:** P1  
**Persona:** Registrar Staff  
**Story:**  
As **registrar staff**, I want to **see a daily payment reconciliation report** so that **I can verify all payments match document requests and catch any discrepancies**.

**Acceptance Criteria:**

**Given** I am logged in as registrar staff with admin role  
**When** I navigate to "Payment Reconciliation" dashboard  
**Then** I should see:
- Date range picker (defaults to today)
- Summary: Total requests paid (50), Total revenue (₱8,500), Successful webhooks (48), Failed webhooks (2)
- Table: Request ID, Student, Amount, Payment Method, Payment Time, Webhook Status, Document Status
- Filter: All, Successfully Reconciled, Pending Reconciliation, Failed
- "Export to Excel" button for accounting

**Given** the "Failed Webhooks" count is 2  
**When** I filter by "Failed"  
**Then** I should see:
- The 2 requests with payment confirmed in Paymongo but webhook not processed
- "Verify in Paymongo" button that opens Paymongo dashboard in new tab
- "Manually Reconcile" button for each request

**Given** I verify payment in Paymongo dashboard and it's legitimate  
**When** I click "Manually Reconcile"  
**Then** the system should:
- Show confirmation modal: "Confirm payment of ₱100 for Request REQ-123?"
- Upon confirmation: Update request status to "Payment Confirmed"
- Dispatch document generation job
- Send email to student (standard "payment received" template)
- Log manual reconciliation with my user ID and timestamp

**Given** I want to generate end-of-day report for accounting  
**When** I click "Export to Excel"  
**Then** the system should:
- Generate Excel file with columns: Date, Request ID, Student ID, Student Name, Document Type, Amount, Payment Method, Payment Time, Receipt Number
- Include summary row: Total Transactions, Total Revenue
- Download file named "Payment_Report_2025-10-04.xlsx"

---

## Module 3: USG Transparency Portal

### Epic: Public Information Management

---

#### Story USG-001: Update VMGO Content
**Priority:** P0  
**Persona:** James Reyes (USG Officer)  
**Story:**  
As a **USG officer with content editor role**, I want to **update VMGO information using a rich text editor** so that **the public sees current institutional goals without needing a developer**.

**Acceptance Criteria:**

**Given** I am logged in as USG officer with "content_editor" role  
**When** I navigate to "VMGO Management"  
**Then** I should see:
- Tabs for: Vision, Mission, Goals, Objectives
- Current published content displayed in preview mode
- "Edit" button for each section
- Version history sidebar showing past 5 revisions with dates and editors

**Given** I click "Edit" on the "Vision" tab  
**When** the editor opens  
**Then** I should see:
- Rich text editor (TipTap or similar) with formatting toolbar: Bold, Italic, Underline, Headings, Bullets, Links
- Current vision text populated in editor
- Character count (max 1000 characters)
- "Save Draft" and "Publish" buttons
- "Cancel" button

**Given** I make changes to vision text  
**When** I click "Save Draft"  
**Then** the system should:
- Save content with status "Draft"
- Not update public-facing page yet
- Display message: "Draft saved. Remember to publish when ready."
- Keep "Publish" button enabled

**Given** I click "Publish"  
**When** the confirmation modal appears  
**Then** it should show:
- "Publish changes to Vision? This will be visible to the public immediately."
- Side-by-side diff view (current vs. new)
- "Confirm Publish" and "Cancel" buttons

**Given** I click "Confirm Publish"  
**When** publishing completes  
**Then** the system should:
- Update public VMGO page immediately (cache invalidation)
- Create version history entry with my user ID and timestamp
- Send email to USG President and Advisor: "VMGO Vision updated by [My Name]"
- Display success message: "Vision published successfully!"

**Given** I made a mistake and want to revert  
**When** I click a previous version in history sidebar  
**Then** I should see:
- Full content of that version in read-only view
- "Restore This Version" button
- Warning: "This will replace current published version"

**Given** I click "Restore This Version"  
**When** restoration completes  
**Then** the system should:
- Restore old content as new published version (creates new version entry, doesn't delete history)
- Update public page
- Log restoration action for audit

---

#### Story USG-002: Manage Officer Directory
**Priority:** P0  
**Persona:** James Reyes (USG President)  
**Story:**  
As **USG President**, I want to **add and update officer profiles with photos** so that **the public knows who's serving them and how to contact us**.

**Acceptance Criteria:**

**Given** I am logged in as USG President  
**When** I navigate to "Officer Management"  
**Then** I should see:
- Grid of current officers with photos, names, positions
- "Add Officer" button
- Edit/Remove buttons on each officer card
- "View Public Page" link to see live directory

**Given** I click "Add Officer"  
**When** the form opens  
**Then** I should see fields for:
- Select User (searchable dropdown of enrolled students)
- Position dropdown (President, Vice President, Secretary, Treasurer, Auditor, PIO, etc.)
- Department dropdown (Executive, Legislative, Administrative, etc.)
- Term start date (defaults to current AY start)
- Term end date (defaults to current AY end)
- Office hours (optional text field)
- Contact email (optional, pre-filled from user account)
- Photo upload (required, max 2MB, min 400x400px)

**Given** I fill all required fields and upload photo  
**When** I click "Save Officer"  
**Then** the system should:
- Validate photo dimensions and size
- Crop/resize photo to square aspect ratio if needed
- Grant selected user the "usg_officer" role
- Create officer profile linked to user account
- Update public officer directory immediately
- Display success message: "[Officer Name] added as [Position]"

**Given** an officer's term has ended  
**When** the system runs daily status check  
**Then** it should:
- Auto-update officer status to "Past Officer"
- Move profile to "Past Officers" archive page
- Revoke "usg_officer" role from user account
- Send email to me: "Term ended for [Officer Name]. Update officer roster."

**Given** I want to update an officer's photo  
**When** I click "Edit" and upload new photo  
**Then** the system should:
- Replace old photo in cloud storage (delete old, upload new)
- Update public directory immediately
- Keep version history showing photo was changed (for audit)

**Given** a visitor views the public officer directory  
**When** they navigate to /usg/officers  
**Then** they should see:
- Filterable grid of officers: "All", "Executive Board", "Legislative", etc.
- Each card showing: Photo, Name, Position, Department, Office Hours, Email (if provided)
- Click on officer card opens modal with full bio and larger photo
- "Past Officers" tab showing previous terms (archived)

---

#### Story USG-003: Publish and Archive Resolutions
**Priority:** P1  
**Persona:** James Reyes (USG Officer)  
**Story:**  
As a **USG officer**, I want to **publish resolutions with PDF attachments** so that **students can search and reference our official decisions**.

**Acceptance Criteria:**

**Given** I am logged in as USG officer  
**When** I navigate to "Resolutions" and click "Add Resolution"  
**Then** I should see a form with:
- Resolution number (auto-generated based on pattern: YEAR-###, e.g., 2025-001)
- Title (required, max 200 chars)
- Category dropdown (Academic Affairs, Student Welfare, Financial, Organizational, etc.)
- Date filed (date picker, defaults to today)
- Status dropdown (Pending, Approved, Implemented, Rejected, Superseded)
- Summary (textarea, max 500 chars for preview)
- PDF upload (required, max 10MB, must be searchable text)
- Visibility radio buttons: Public, Restricted (for sensitive personnel matters)

**Given** I fill all fields and upload resolution PDF  
**When** I click "Publish Resolution"  
**Then** the system should:
- Validate PDF is text-searchable (OCR if scanned image)
- Upload PDF to secure cloud storage
- Create resolution entry in database
- Publish immediately to public archive (if visibility = Public)
- Send email to USG Advisor and Student Body President (if major resolution)
- Display success message: "Resolution 2025-001 published"

**Given** a student visits the public resolutions archive  
**When** they navigate to /usg/resolutions  
**Then** they should see:
- Search bar for keyword search (searches title, summary, PDF text)
- Filter dropdowns: Year, Category, Status
- Table of resolutions: Number, Title, Date Filed, Category, Status, Actions
- "View PDF" button opens integrated PDF viewer in modal (no download required)
- "Download" button for offline access

**Given** a student searches for "scholarship"  
**When** they submit search  
**Then** the system should:
- Full-text search across resolution titles, summaries, and PDF content
- Highlight matching text in results
- Display results sorted by relevance
- Show snippet of matching text: "...increasing scholarship allocation from ₱500,000 to ₱750,000..."

**Given** I need to mark an old resolution as "Superseded" by a new one  
**When** I edit the old resolution  
**Then** I should see:
- "Superseded By" field where I can select another resolution number
- Upon saving, old resolution displays banner: "This resolution has been superseded by 2025-045"
- Link to new resolution provided

---

#### Story USG-004: Create and Schedule Announcements
**Priority:** P0  
**Persona:** James Reyes (USG Officer)  
**Story:**  
As a **USG officer**, I want to **create announcements with scheduling and expiration** so that **important information reaches students at the right time without manual intervention**.

**Acceptance Criteria:**

**Given** I am logged in as USG officer  
**When** I navigate to "Announcements" and click "Create Announcement"  
**Then** I should see:
- Title field (required, max 150 chars)
- Content (rich text editor, max 2000 chars)
- Priority dropdown: Normal, Urgent (yellow banner), Critical (red banner + homepage alert)
- Category checkboxes: Academic, Deadlines, Events, General, Health & Safety
- Publish date/time picker (defaults to "Publish immediately")
- Expiration date picker (optional, auto-archives after this date)
- Feature announcement checkbox (pins to top of list, max 3 featured at once)
- Banner image upload (optional, 1200x630px recommended)

**Given** I set priority to "Critical"  
**When** I save the announcement  
**Then** the system should:
- Display red alert banner on homepage: "[URGENT] Title - Brief preview... [Read More]"
- Show red "Critical" badge on announcement list
- Send push notification to all students subscribed to announcements (future feature)
- Keep banner visible until expiration date or until I manually dismiss

**Given** I schedule announcement for future publication (e.g., Oct 10, 8 AM)  
**When** I click "Schedule"  
**Then** the system should:
- Save announcement with status "Scheduled"
- Show in my drafts/scheduled list with countdown: "Publishes in 3 days"
- At scheduled time, automatically publish to public page
- Send me confirmation email: "Your announcement '[Title]' is now live"

**Given** an announcement expires (expiration date reached)  
**When** the system runs hourly check  
**Then** it should:
- Auto-update status to "Archived"
- Remove from main announcements page
- Move to "Archived Announcements" (still searchable)
- Remove homepage banner if it was featured

**Given** I want to edit a published announcement  
**When** I click "Edit"  
**Then** I should see:
- Warning: "This announcement is already public. Changes will be visible immediately."
- Edit log showing: "Last edited by [Name] on [Date]"
- Upon saving, system logs edit in audit trail (who changed what, when)

**Given** I feature 4th announcement when 3 are already featured  
**When** I try to save  
**Then** the system should:
- Prevent action
- Display error: "Maximum 3 featured announcements. Unfeature one to proceed."
- Show list of currently featured announcements with "Unfeature" buttons

---

## Cross-Cutting User Stories

### Epic: Authentication & User Management

---

#### Story AUTH-001: Register New Student Account
**Priority:** P0  
**Persona:** Maria Santos (New Student)  
**Story:**  
As a **new student**, I want to **register using my student ID and school email** so that **I can access all system features securely**.

**Acceptance Criteria:**

**Given** I visit the system landing page  
**When** I click "Register"  
**Then** I should see a registration form with:
- Student ID field (required, format validation: XXX-XXXX-XXX)
- School email field (required, must end with @minsubongabong.edu.ph)
- Password field (min 8 chars, requires 1 uppercase, 1 number, 1 symbol)
- Confirm password field
- Terms of service checkbox
- "Create Account" button

**Given** I enter valid student ID and school email  
**When** I click "Create Account"  
**Then** the system should:
- Verify student ID exists in registrar database
- Check email not already registered
- Send verification email to school email with 6-digit code
- Display: "Verification code sent to your email. Please check inbox/spam."
- Show code input field

**Given** I enter the correct verification code  
**When** I click "Verify Email"  
**Then** the system should:
- Activate my account
- Auto-login and redirect to student dashboard
- Send welcome email with getting started guide
- Display success message: "Welcome to SAS Information System!"

**Given** I enter an invalid student ID  
**When** I attempt registration  
**Then** the system should:
- Display error: "Student ID not found in records. Contact registrar if this is incorrect."
- Prevent account creation

**Given** I use a non-school email (e.g., Gmail)  
**When** I attempt registration  
**Then** the system should:
- Display error: "Please use your official MinSU Bongabong email (@minsubongabong.edu.ph)"
- Prevent registration (security measure to verify legitimate students)

---

#### Story AUTH-002: Login with Session Persistence
**Priority:** P0  
**Persona:** All Users  
**Story:**  
As a **user**, I want to **login securely and stay logged in on trusted devices** so that **I don't have to re-enter credentials every visit**.

**Acceptance Criteria:**

**Given** I visit the login page  
**When** I see the login form  
**Then** I should see:
- Email or Student ID field
- Password field (with show/hide toggle)
- "Remember me" checkbox
- "Forgot password?" link
- "Login" button

**Given** I enter correct credentials and check "Remember me"  
**When** I click "Login"  
**Then** the system should:
- Authenticate via Laravel Fortify
- Create session cookie (expires in 2 weeks if "Remember me" checked, otherwise on browser close)
- Redirect to appropriate dashboard based on role: Student → /dashboard, SAS Staff → /sas/dashboard, Registrar → /registrar/dashboard
- Log login timestamp and IP address for security audit

**Given** I enter incorrect password  
**When** I attempt login  
**Then** the system should:
- Display error: "Invalid credentials. Please try again."
- Increment failed login attempt counter
- After 5 failed attempts: Lock account for 15 minutes and send email alert: "Multiple failed login attempts detected"

**Given** I click "Forgot password?"  
**When** the password reset page loads  
**Then** I should see:
- Email field
- "Send Reset Link" button

**Given** I enter my registered email  
**When** I click "Send Reset Link"  
**Then** the system should:
- Generate secure password reset token (expires in 1 hour)
- Send email with reset link: "Click here to reset your password: [link]"
- Display: "Password reset link sent. Check your email."

**Given** I click the reset link in email  
**When** the reset page loads  
**Then** I should see:
- New password field
- Confirm new password field
- "Reset Password" button
- Upon successful reset: Auto-login and redirect to dashboard with message "Password reset successful!"

---

## Epic: Notifications & Communication

---

#### Story NOTIF-001: Receive Email Notifications
**Priority:** P0  
**Persona:** All Users  
**Story:**  
As a **user**, I want to **receive email notifications for important actions** so that **I stay informed without constantly checking the website**.

**Acceptance Criteria:**

**Given** my scholarship application is approved  
**When** SAS staff clicks "Approve"  
**Then** I should receive email:
- **Subject:** "Scholarship Application Approved - SA-2025-0001"
- **Body:** Personalized message with approval details, scholarship type, amount, next steps
- **CTA Button:** "View Application"
- **Footer:** Unsubscribe link (for non-critical emails only)

**Given** my document request is ready for download  
**When** PDF generation completes  
**Then** I should receive email:
- **Subject:** "Your TOR is Ready for Download"
- **Body:** "Your requested Transcript of Records is now available. Download link expires in 30 days."
- **CTA Button:** "Download Document"
- **Additional:** Official receipt attached as PDF

**Given** I haven't downloaded my document within 7 days  
**When** the system runs daily reminder check  
**Then** I should receive email:
- **Subject:** "Reminder: Your Document is Ready for Download"
- **Body:** "You requested a TOR on Oct 4. It's been ready for 7 days and will expire in 23 days."
- **CTA Button:** "Download Now"

**Given** I want to manage notification preferences  
**When** I navigate to Account Settings → Notifications  
**Then** I should see toggles for:
- Scholarship updates (Email: On, SMS: Off)
- Document request updates (Email: On, SMS: On)
- Event reminders (Email: On, SMS: Off)
- Announcements (Email: On, SMS: Off)
- System updates (Email: On, cannot disable)

**Given** I disable "Event reminders" notifications  
**When** I save preferences  
**Then** the system should:
- Update my notification settings
- Stop sending event reminder emails to me
- Display confirmation: "Notification preferences updated"

---

#### Story NOTIF-002: View In-App Notification Center
**Priority:** P1  
**Persona:** All Users  
**Story:**  
As a **user**, I want to **see all my notifications in-app** so that **I have a backup if I miss emails**.

**Acceptance Criteria:**

**Given** I am logged in  
**When** I look at the top navigation bar  
**Then** I should see:
- Bell icon with red badge showing unread count (e.g., "3")
- Clicking bell opens dropdown notification panel

**Given** I click the bell icon  
**When** the notification panel opens  
**Then** I should see:
- List of recent notifications (last 10)
- Each notification showing: Icon (based on type), Title, Time ago (e.g., "2 hours ago"), Unread indicator (blue dot)
- "Mark all as read" button
- "View all notifications" link (goes to full page)

**Given** I click on a notification  
**When** the action triggers  
**Then** the system should:
- Mark notification as read (remove blue dot, decrement badge count)
- Navigate to relevant page (e.g., scholarship application detail, document download page)

**Given** I click "View all notifications"  
**When** the full page loads  
**Then** I should see:
- All notifications paginated (20 per page)
- Filter tabs: All, Unread, Scholarships, Documents, Events, System
- Each notification expandable to show full message
- Delete button for each notification (soft delete, keeps in audit log)

**Given** a new notification arrives while I'm logged in  
**When** the system pushes the update  
**Then** I should see:
- Bell badge count increment (via WebSocket or polling)
- Toast notification in bottom-right corner: "[Title] - [Brief preview]" (auto-dismisses after 5 seconds)
- Option to click toast to view full notification

---

## Summary: User Story Coverage

| Module | Epic | Stories Created | P0 Stories | P1 Stories | P2 Stories |
|--------|------|-----------------|------------|------------|------------|
| SAS | Scholarship Management | 3 | 3 | 0 | 0 |
| SAS | Organization Management | 3 | 2 | 1 | 0 |
| SAS | Event Calendar | 2 | 2 | 0 | 0 |
| Registrar | Document Request Workflow | 3 | 3 | 0 | 0 |
| Registrar | Payment Processing | 2 | 1 | 1 | 0 |
| USG | Public Information | 4 | 3 | 1 | 0 |
| Cross-Cutting | Authentication | 2 | 2 | 0 | 0 |
| Cross-Cutting | Notifications | 2 | 1 | 1 | 0 |
| **TOTAL** | **8 Epics** | **21 Stories** | **17** | **4** | **0** |

---

## Next Steps: Implementation Planning

1. **Sprint Planning**: Group P0 stories into 2-week sprints
2. **Technical Tasks**: Break each user story into technical tasks (API endpoints, database migrations, UI components)
3. **Test Case Creation**: Convert acceptance criteria into automated test cases (Pest)
4. **Design Mockups**: Create UI mockups for key user flows before development
5. **API Contract Definition**: Document API specifications for frontend-backend integration

---

**Document Maintenance:**
- Update acceptance criteria as edge cases are discovered during development
- Add new stories to backlog as stakeholder feedback comes in
- Mark stories "Done" when all acceptance criteria pass in production

