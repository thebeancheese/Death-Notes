# Memento Vitae ERD

This ERD was derived from [`mementovitae.sql`](C:/Users/Zen/Downloads/Memento_Vitae/mementovitae.sql) and cross-checked against the PHP workflow in [`includes/db.php`](C:/Users/Zen/Downloads/Memento_Vitae/includes/db.php).

## Mermaid ERD

```mermaid
erDiagram
    ROLES ||--o{ USERS : assigns
    USERS ||--o{ DEATH_RECORDS : applies_for
    USERS ||--o{ DEATH_RECORDS : creates
    USERS o|--o{ DEATH_RECORDS : archives
    DEATH_RECORDS ||--o{ DEATH_CERTIFICATE_REQUESTS : receives
    USERS ||--o{ DEATH_CERTIFICATE_REQUESTS : submits
    USERS ||--o{ EMAIL_VERIFICATION_TOKENS : owns
    USERS ||--o{ PASSWORD_RESET_TOKENS : owns
    USERS ||--o{ SOCIAL_ACCOUNTS : links
    DEATH_RECORDS ||--o{ RECORD_DOCUMENTS : has
    USERS ||--o{ RECORD_DOCUMENTS : uploads
    USERS o|--o{ RECORD_DOCUMENTS : reviews

    USERS ||--o{ NOTIFICATIONS : receives
    DEATH_RECORDS o|--o{ NOTIFICATIONS : relates_to

    USERS ||--o{ RECORD_ACTIVITY_LOGS : acts_in
    USERS o|--o{ RECORD_ACTIVITY_LOGS : affects
    DEATH_RECORDS o|--o{ RECORD_ACTIVITY_LOGS : tracks

    ROLES {
        int role_id PK
        varchar role_name
    }

    USERS {
        int user_id PK
        varchar full_name
        varchar email UK
        varchar password
        int role_id FK
        varchar status
        datetime email_verified_at
        timestamp created_at
    }

    DEATH_RECORDS {
        int record_id PK
        varchar tracking_code UK
        varchar deceased_name
        date date_of_death
        varchar place_of_death
        varchar cause_of_death
        varchar informant_name
        varchar relationship
        int applicant_user_id FK
        int created_by FK
        varchar status
        timestamp date_submitted
        datetime deleted_at
        int deleted_by FK
    }

    RECORD_DOCUMENTS {
        int document_id PK
        int record_id FK
        varchar document_type
        varchar original_file_name
        varchar stored_file_name
        varchar file_path
        varchar mime_type
        int file_size
        int uploaded_by FK
        varchar review_status
        text review_notes
        int reviewed_by FK
        datetime reviewed_at
        timestamp uploaded_at
    }

    DEATH_CERTIFICATE_REQUESTS {
        int request_id PK
        int record_id FK
        int requester_user_id FK
        varchar recipient_email
        varchar purpose
        int copies_requested
        varchar contact_number
        text remarks
        varchar status
        varchar email_status
        text email_error
        timestamp submitted_at
        datetime emailed_at
    }

    EMAIL_VERIFICATION_TOKENS {
        int verification_id PK
        int user_id FK
        char token_hash UK
        datetime expires_at
        datetime used_at
        timestamp created_at
    }

    PASSWORD_RESET_TOKENS {
        int reset_id PK
        int user_id FK
        char token_hash UK
        datetime expires_at
        datetime used_at
        timestamp created_at
    }

    SOCIAL_ACCOUNTS {
        int social_account_id PK
        int user_id FK
        varchar provider
        varchar provider_user_id
        varchar provider_email
        timestamp created_at
        timestamp last_login_at
    }

    NOTIFICATIONS {
        int notification_id PK
        int user_id
        int related_record_id
        varchar title
        text message
        tinyint is_read
        timestamp created_at
    }

    RECORD_ACTIVITY_LOGS {
        int log_id PK
        int related_record_id
        int actor_user_id
        int affected_user_id
        varchar action_type
        varchar old_status
        varchar new_status
        text remarks
        text details
        timestamp created_at
    }
```

## Relationship Notes

- Physical foreign keys exist for `users.role_id`, all token tables, `social_accounts.user_id`, `death_records.applicant_user_id`, `death_records.created_by`, `death_records.deleted_by`, `record_documents.record_id`, `record_documents.uploaded_by`, `record_documents.reviewed_by`, `death_certificate_requests.record_id`, and `death_certificate_requests.requester_user_id`.
- `notifications.user_id`, `notifications.related_record_id`, `record_activity_logs.related_record_id`, `record_activity_logs.actor_user_id`, and `record_activity_logs.affected_user_id` are used as references in the application, but the SQL dump does not enforce them with foreign key constraints.
- `death_records.deleted_at` and `death_records.deleted_by` implement soft delete and restore behavior rather than permanently removing records.

## Controlled Values Used By The App

- `roles.role_name`: `Admin`, `Barangay Staff`, `User`
- `death_records.status`: `Pending`, `Verified`, `Approved`, `Rejected`
- `record_documents.document_type`: `Medical Certificate`, `Autopsy Report`, `Valid ID`, `Supporting Affidavit`
- `record_documents.review_status`: `Pending Review`, `Valid`, `Needs Replacement`, `Rejected`
- `death_certificate_requests.status`: currently created as `Submitted`
- `death_certificate_requests.email_status`: `Pending`, `Sent`, `Failed`
- `users.status`: observed as `active` in registration, admin creation, social sign-in, and email verification flows

## Workflow Summary

- A `User` submits or is assigned a `death_record`.
- Staff or admins manage the `death_record` lifecycle.
- The applicant uploads `record_documents`.
- Staff review documents before a record can move to `Verified`.
- Only `Approved` records can generate `death_certificate_requests`.
- User-facing alerts are stored in `notifications`.
- Auditing and workflow history are stored in `record_activity_logs`.
