# Memento Vitae Data Dictionary

This dictionary was based on [`mementovitae.sql`](C:/Users/Zen/Downloads/Memento_Vitae/mementovitae.sql) and validated against how the PHP application reads and writes the data.

If you are following the 6DWEB Course Output PDF **only up to the section before page 6, "Essentials for Information Management (6IMAN) Documentation,"** use [`6DWEB_DATABASE_DESIGN.md`](C:/Users/Zen/Downloads/Memento_Vitae/docs/6DWEB_DATABASE_DESIGN.md) as the main submission-ready version.

This file is a more detailed table-by-table dictionary and is best treated as an extended appendix or an advanced database reference.

## Table Overview

| Table | Purpose |
| --- | --- |
| `roles` | Master list of application roles. |
| `users` | System accounts for admins, barangay staff, and end users. |
| `death_records` | Main death registration/application records tracked by the system. |
| `record_documents` | Uploaded supporting files tied to a death record. |
| `death_certificate_requests` | Requests for death certificates after a record is approved. |
| `notifications` | User-facing workflow notifications. |
| `record_activity_logs` | Audit trail and workflow history. |
| `email_verification_tokens` | One-time email verification tokens. |
| `password_reset_tokens` | One-time password reset tokens. |
| `social_accounts` | Linked Google or other social login identities. |

## `roles`

| Column | Type | Null | Key / Default | Description |
| --- | --- | --- | --- | --- |
| `role_id` | `int(11)` | No | PK | Numeric role identifier. |
| `role_name` | `varchar(50)` | No | - | Human-readable role name. Seeded values are `Admin`, `Barangay Staff`, and `User`. |

## `users`

| Column | Type | Null | Key / Default | Description |
| --- | --- | --- | --- | --- |
| `user_id` | `int(11)` | No | PK, auto increment | Unique user identifier. |
| `full_name` | `varchar(100)` | No | - | Person's full name. |
| `email` | `varchar(120)` | No | Unique | Login email and contact email. |
| `password` | `varchar(255)` | No | - | Hashed password. Social accounts also receive a generated hash. |
| `role_id` | `int(11)` | No | FK, default `3` | References `roles.role_id`; `3` maps to normal user. |
| `status` | `varchar(20)` | No | default `active` | Account state. The app currently sets and expects `active`. |
| `email_verified_at` | `datetime` | Yes | `NULL` | Timestamp when email was verified. Used to gate login and password reset. |
| `created_at` | `timestamp` | No | `current_timestamp()` | Account creation timestamp. |

## `death_records`

| Column | Type | Null | Key / Default | Description |
| --- | --- | --- | --- | --- |
| `record_id` | `int(11)` | No | PK, auto increment | Unique death record identifier. |
| `tracking_code` | `varchar(20)` | Yes | Unique | Public reference code such as `DN-YYYYMMDD-XXXXXX`. |
| `deceased_name` | `varchar(150)` | No | - | Name of the deceased person. |
| `date_of_death` | `date` | No | - | Date of death. |
| `place_of_death` | `varchar(150)` | No | - | Place where death occurred. |
| `cause_of_death` | `varchar(200)` | No | - | Recorded cause of death. |
| `informant_name` | `varchar(150)` | No | - | Name of the person reporting the death. |
| `relationship` | `varchar(50)` | No | - | Relationship of the informant to the deceased. |
| `applicant_user_id` | `int(11)` | No | FK | End user tied to the application. References `users.user_id`. |
| `created_by` | `int(11)` | No | FK | Staff/admin who created the record. References `users.user_id`. |
| `status` | `varchar(30)` | Yes | default `Pending` | Workflow status. App options are `Pending`, `Verified`, `Approved`, `Rejected`. |
| `date_submitted` | `timestamp` | No | `current_timestamp()` | Submission/creation timestamp. |
| `deleted_at` | `datetime` | Yes | `NULL` | Soft delete timestamp for archived records. |
| `deleted_by` | `int(11)` | Yes | FK, `NULL` | User who archived the record. Set to `NULL` again on restore. |

Business rules:

- A record must have at least one `Medical Certificate` or `Autopsy Report` marked `Valid` before it can become `Verified`.
- A record must already be `Verified` before it can become `Approved`.
- Archived records are soft-deleted, not removed from the table.

## `record_documents`

| Column | Type | Null | Key / Default | Description |
| --- | --- | --- | --- | --- |
| `document_id` | `int(11)` | No | PK, auto increment | Unique uploaded document identifier. |
| `record_id` | `int(11)` | No | FK | Parent death record. References `death_records.record_id`. |
| `document_type` | `varchar(80)` | No | - | App-controlled type: `Medical Certificate`, `Autopsy Report`, `Valid ID`, or `Supporting Affidavit`. |
| `original_file_name` | `varchar(255)` | No | - | Original file name from the user upload. |
| `stored_file_name` | `varchar(255)` | No | - | Server-side generated file name. |
| `file_path` | `varchar(255)` | No | - | Relative storage path used for download. |
| `mime_type` | `varchar(120)` | Yes | `NULL` | Detected MIME type. |
| `file_size` | `int(11)` | No | default `0` | File size in bytes. |
| `uploaded_by` | `int(11)` | No | FK | User who uploaded the file. References `users.user_id`. |
| `review_status` | `varchar(30)` | No | default `Pending Review` | Staff review state: `Pending Review`, `Valid`, `Needs Replacement`, or `Rejected`. |
| `review_notes` | `text` | Yes | `NULL` | Reviewer comments or replacement instructions. |
| `reviewed_by` | `int(11)` | Yes | FK, `NULL` | Reviewer account. References `users.user_id`. |
| `reviewed_at` | `datetime` | Yes | `NULL` | When the review decision was saved. |
| `uploaded_at` | `timestamp` | No | `current_timestamp()` | Upload timestamp. |

Operational notes:

- The app currently allows `pdf`, `jpg`, `jpeg`, and `png`.
- The upload size limit enforced in PHP is 5 MB.

## `death_certificate_requests`

| Column | Type | Null | Key / Default | Description |
| --- | --- | --- | --- | --- |
| `request_id` | `int(11)` | No | PK, auto increment | Unique certificate request identifier. |
| `record_id` | `int(11)` | No | FK | Approved death record being requested. References `death_records.record_id`. |
| `requester_user_id` | `int(11)` | No | FK | User who submitted the request. References `users.user_id`. |
| `recipient_email` | `varchar(190)` | No | - | Configured registry email address that receives the request. |
| `purpose` | `varchar(150)` | No | - | Reason for requesting the certificate. |
| `copies_requested` | `int(11)` | No | default `1` | Number of copies requested. PHP currently allows `1` to `10`. |
| `contact_number` | `varchar(40)` | Yes | `NULL` | Optional requester contact number. |
| `remarks` | `text` | Yes | `NULL` | Optional notes for the registry office. |
| `status` | `varchar(30)` | No | default `Submitted` | Workflow/request state. The current app inserts `Submitted`. |
| `email_status` | `varchar(30)` | No | default `Pending` | Delivery state for the outgoing email. Observed values: `Pending`, `Sent`, `Failed`. |
| `email_error` | `text` | Yes | `NULL` | SMTP or delivery error captured when sending fails. |
| `submitted_at` | `timestamp` | No | `current_timestamp()` | Request submission timestamp. |
| `emailed_at` | `datetime` | Yes | `NULL` | Timestamp when email was successfully sent. |

Business rules:

- The app only allows a request when `death_records.status = 'Approved'`.
- After insert, the system attempts to email the registry office and updates `email_status`.

## `notifications`

| Column | Type | Null | Key / Default | Description |
| --- | --- | --- | --- | --- |
| `notification_id` | `int(11)` | No | PK, auto increment | Unique notification identifier. |
| `user_id` | `int(11)` | No | Indexed | Recipient user account. Used by the app as a reference to `users.user_id`. |
| `related_record_id` | `int(11)` | Yes | Indexed via app queries only | Related death record, when applicable. Used by the app as a reference to `death_records.record_id`. |
| `title` | `varchar(150)` | No | - | Notification headline. |
| `message` | `text` | No | - | Notification body shown to the user. |
| `is_read` | `tinyint(1)` | No | default `0` | Read flag: `0` unread, `1` read. |
| `created_at` | `timestamp` | No | `current_timestamp()` | Notification creation timestamp. |

Important note:

- The SQL dump defines indexes for this table but no foreign key constraints, even though the app treats `user_id` and `related_record_id` as references.

## `record_activity_logs`

| Column | Type | Null | Key / Default | Description |
| --- | --- | --- | --- | --- |
| `log_id` | `int(11)` | No | PK, auto increment | Unique log identifier. |
| `related_record_id` | `int(11)` | Yes | Indexed | Death record involved in the event. App-level reference to `death_records.record_id`. |
| `actor_user_id` | `int(11)` | No | Indexed | User who performed the action. App-level reference to `users.user_id`. |
| `affected_user_id` | `int(11)` | Yes | Indexed | User affected by the event, commonly the applicant. App-level reference to `users.user_id`. |
| `action_type` | `varchar(50)` | No | - | Machine-friendly action label. |
| `old_status` | `varchar(30)` | Yes | `NULL` | Previous workflow status, when status changes are involved. |
| `new_status` | `varchar(30)` | Yes | `NULL` | New workflow status, when status changes are involved. |
| `remarks` | `text` | Yes | `NULL` | Optional note saved with the event. |
| `details` | `text` | Yes | `NULL` | Human-readable event description. |
| `created_at` | `timestamp` | No | `current_timestamp()` | Event timestamp. |

Observed `action_type` values in the application:

- `record_created`
- `record_updated`
- `record_archived`
- `record_restored`
- `status_updated`
- `status_note`
- `document_uploaded`
- `document_reviewed`
- `certificate_requested`
- `account_created_google`

Important note:

- Like `notifications`, this table is indexed but not protected by foreign key constraints in the SQL dump.

## `email_verification_tokens`

| Column | Type | Null | Key / Default | Description |
| --- | --- | --- | --- | --- |
| `verification_id` | `int(11)` | No | PK, auto increment | Unique verification token row. |
| `user_id` | `int(11)` | No | FK | Owner of the token. References `users.user_id`. |
| `token_hash` | `char(64)` | No | Unique | SHA-256 hash of the raw token sent by email. |
| `expires_at` | `datetime` | No | - | Expiration timestamp. |
| `used_at` | `datetime` | Yes | `NULL` | Set when the token is consumed or invalidated. |
| `created_at` | `timestamp` | No | `current_timestamp()` | Token creation timestamp. |

Operational notes:

- PHP invalidates previous unused verification tokens for the same user before issuing a new one.
- Verification links are currently issued with a 24-hour TTL.

## `password_reset_tokens`

| Column | Type | Null | Key / Default | Description |
| --- | --- | --- | --- | --- |
| `reset_id` | `int(11)` | No | PK, auto increment | Unique reset token row. |
| `user_id` | `int(11)` | No | FK | Owner of the token. References `users.user_id`. |
| `token_hash` | `char(64)` | No | Unique | SHA-256 hash of the raw reset token. |
| `expires_at` | `datetime` | No | - | Expiration timestamp. |
| `used_at` | `datetime` | Yes | `NULL` | Set when the token is consumed or invalidated. |
| `created_at` | `timestamp` | No | `current_timestamp()` | Token creation timestamp. |

Operational notes:

- PHP invalidates previous unused reset tokens for the same user before issuing a new one.
- Reset links are currently issued with a 2-hour TTL.

## `social_accounts`

| Column | Type | Null | Key / Default | Description |
| --- | --- | --- | --- | --- |
| `social_account_id` | `int(11)` | No | PK, auto increment | Unique linked social account identifier. |
| `user_id` | `int(11)` | No | FK | Local user account linked to the provider identity. References `users.user_id`. |
| `provider` | `varchar(30)` | No | Part of unique key | Social auth provider name, such as Google. |
| `provider_user_id` | `varchar(191)` | No | Part of unique key | Provider-side subject or account identifier. |
| `provider_email` | `varchar(191)` | Yes | `NULL` | Email returned by the provider. |
| `created_at` | `timestamp` | No | `current_timestamp()` | Link creation timestamp. |
| `last_login_at` | `timestamp` | Yes | `NULL` | Last login time recorded through the provider. |

Business rule:

- The pair (`provider`, `provider_user_id`) must be unique across the system.

## Recommended FK Hardening

These references are already used by the application and would be good candidates for explicit database constraints in a future revision:

- `notifications.user_id -> users.user_id`
- `notifications.related_record_id -> death_records.record_id`
- `record_activity_logs.related_record_id -> death_records.record_id`
- `record_activity_logs.actor_user_id -> users.user_id`
- `record_activity_logs.affected_user_id -> users.user_id`
