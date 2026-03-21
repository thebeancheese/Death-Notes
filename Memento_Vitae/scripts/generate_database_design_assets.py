from __future__ import annotations

from html import escape
from pathlib import Path


ROOT = Path(__file__).resolve().parents[1]
DOCS = ROOT / "docs"
DOCS.mkdir(exist_ok=True)


STRICT_ENTITIES = [
    {
        "name": "ROLES",
        "x": 300,
        "y": 40,
        "fields": ["role_id (PK)", "role_name"],
    },
    {
        "name": "EMAIL_VERIFICATION_TOKENS",
        "x": 40,
        "y": 220,
        "fields": ["verification_id (PK)", "user_id (FK)", "token_hash (UK)", "expires_at", "used_at", "created_at"],
    },
    {
        "name": "USERS",
        "x": 300,
        "y": 230,
        "fields": ["user_id (PK)", "full_name", "email (UK)", "password", "role_id (FK)", "status", "email_verified_at", "created_at"],
    },
    {
        "name": "PASSWORD_RESET_TOKENS",
        "x": 560,
        "y": 220,
        "fields": ["reset_id (PK)", "user_id (FK)", "token_hash (UK)", "expires_at", "used_at", "created_at"],
    },
    {
        "name": "DEATH_RECORDS",
        "x": 820,
        "y": 140,
        "fields": [
            "record_id (PK)",
            "tracking_code (UK)",
            "deceased_name",
            "date_of_death",
            "place_of_death",
            "cause_of_death",
            "informant_name",
            "relationship",
            "applicant_user_id (FK)",
            "created_by (FK)",
            "status",
            "date_submitted",
            "deleted_at",
            "deleted_by (FK)",
        ],
    },
    {
        "name": "SOCIAL_ACCOUNTS",
        "x": 300,
        "y": 600,
        "fields": ["social_account_id (PK)", "user_id (FK)", "provider", "provider_user_id", "provider_email", "created_at", "last_login_at"],
    },
    {
        "name": "RECORD_DOCUMENTS",
        "x": 820,
        "y": 660,
        "fields": [
            "document_id (PK)",
            "record_id (FK)",
            "document_type",
            "original_file_name",
            "stored_file_name",
            "file_path",
            "mime_type",
            "file_size",
            "uploaded_by (FK)",
            "review_status",
            "review_notes",
            "reviewed_by (FK)",
            "reviewed_at",
            "uploaded_at",
        ],
    },
    {
        "name": "DEATH_CERTIFICATE_REQUESTS",
        "x": 820,
        "y": 1120,
        "fields": [
            "request_id (PK)",
            "record_id (FK)",
            "requester_user_id (FK)",
            "recipient_email",
            "purpose",
            "copies_requested",
            "contact_number",
            "remarks",
            "status",
            "email_status",
            "email_error",
            "submitted_at",
            "emailed_at",
        ],
    },
]


STRICT_RELATIONS = [
    ("ROLES", "USERS", "1", "many", "role_id"),
    ("USERS", "EMAIL_VERIFICATION_TOKENS", "1", "many", "user_id", "solid", -20),
    ("USERS", "PASSWORD_RESET_TOKENS", "1", "many", "user_id", "solid", 20),
    ("USERS", "SOCIAL_ACCOUNTS", "1", "many", "user_id", "solid", 0),
    ("USERS", "DEATH_RECORDS", "1", "many", "applicant_user_id", "solid", -32),
    ("USERS", "DEATH_RECORDS", "1", "many", "created_by", "solid", 0),
    ("USERS", "DEATH_RECORDS", "0..1", "many", "deleted_by", "solid", 32),
    ("DEATH_RECORDS", "RECORD_DOCUMENTS", "1", "many", "record_id", "solid", 0),
    ("USERS", "RECORD_DOCUMENTS", "1", "many", "uploaded_by", "solid", -18),
    ("USERS", "RECORD_DOCUMENTS", "0..1", "many", "reviewed_by", "solid", 18),
    ("DEATH_RECORDS", "DEATH_CERTIFICATE_REQUESTS", "1", "many", "record_id", "solid", 0),
    ("USERS", "DEATH_CERTIFICATE_REQUESTS", "1", "many", "requester_user_id", "solid", 0),
]


LOGICAL_ENTITIES = [
    {
        "name": "USERS",
        "x": 80,
        "y": 220,
        "fields": ["user_id (PK)", "full_name", "email", "role_id", "status"],
    },
    {
        "name": "DEATH_RECORDS",
        "x": 500,
        "y": 220,
        "fields": ["record_id (PK)", "tracking_code", "deceased_name", "status", "applicant_user_id", "created_by", "deleted_by"],
    },
    {
        "name": "NOTIFICATIONS",
        "x": 930,
        "y": 80,
        "fields": ["notification_id (PK)", "user_id (Reference)", "related_record_id (Reference)", "title", "message", "is_read", "created_at"],
    },
    {
        "name": "RECORD_ACTIVITY_LOGS",
        "x": 930,
        "y": 430,
        "fields": ["log_id (PK)", "related_record_id (Reference)", "actor_user_id (Reference)", "affected_user_id (Reference)", "action_type", "old_status", "new_status", "remarks", "details", "created_at"],
    },
]


LOGICAL_RELATIONS = [
    ("USERS", "NOTIFICATIONS", "1", "many", "user_id", "dashed", -16),
    ("DEATH_RECORDS", "NOTIFICATIONS", "0..1", "many", "related_record_id", "dashed", 16),
    ("USERS", "RECORD_ACTIVITY_LOGS", "1", "many", "actor_user_id", "dashed", -24),
    ("USERS", "RECORD_ACTIVITY_LOGS", "0..1", "many", "affected_user_id", "dashed", 24),
    ("DEATH_RECORDS", "RECORD_ACTIVITY_LOGS", "0..1", "many", "related_record_id", "dashed", 0),
]


TABLE_SUMMARY = {
    "roles": [
        ("role_id", "int(11)", "PK", "Unique role ID."),
        ("role_name", "varchar(50)", "-", "Role name such as Admin, Barangay Staff, or User."),
    ],
    "users": [
        ("user_id", "int(11)", "PK", "Unique user ID."),
        ("full_name", "varchar(100)", "-", "Full name of the account owner."),
        ("email", "varchar(120)", "UK", "User email address."),
        ("password", "varchar(255)", "-", "Hashed password."),
        ("role_id", "int(11)", "FK", "Links the user to a role."),
        ("status", "varchar(20)", "-", "Account status."),
        ("email_verified_at", "datetime", "-", "Date and time the email was verified."),
        ("created_at", "timestamp", "-", "Account creation date and time."),
    ],
    "death_records": [
        ("record_id", "int(11)", "PK", "Unique death record ID."),
        ("tracking_code", "varchar(20)", "UK", "Generated public reference code."),
        ("deceased_name", "varchar(150)", "-", "Name of the deceased."),
        ("date_of_death", "date", "-", "Date of death."),
        ("place_of_death", "varchar(150)", "-", "Place where death occurred."),
        ("cause_of_death", "varchar(200)", "-", "Cause of death."),
        ("informant_name", "varchar(150)", "-", "Name of the informant."),
        ("relationship", "varchar(50)", "-", "Informant's relationship to the deceased."),
        ("applicant_user_id", "int(11)", "FK", "User assigned as applicant."),
        ("created_by", "int(11)", "FK", "Staff/admin who created the record."),
        ("status", "varchar(30)", "-", "Workflow status of the record."),
        ("date_submitted", "timestamp", "-", "Submission timestamp."),
        ("deleted_at", "datetime", "-", "Soft-delete timestamp for archived records."),
        ("deleted_by", "int(11)", "FK", "User who archived the record."),
    ],
    "record_documents": [
        ("document_id", "int(11)", "PK", "Unique document ID."),
        ("record_id", "int(11)", "FK", "Linked death record."),
        ("document_type", "varchar(80)", "-", "Type of uploaded document."),
        ("original_file_name", "varchar(255)", "-", "Original upload filename."),
        ("stored_file_name", "varchar(255)", "-", "Saved filename on the server."),
        ("file_path", "varchar(255)", "-", "File storage path."),
        ("mime_type", "varchar(120)", "-", "MIME type of the file."),
        ("file_size", "int(11)", "-", "File size in bytes."),
        ("uploaded_by", "int(11)", "FK", "User who uploaded the document."),
        ("review_status", "varchar(30)", "-", "Review result for the document."),
        ("review_notes", "text", "-", "Reviewer comments or replacement notes."),
        ("reviewed_by", "int(11)", "FK", "Staff/admin who reviewed the file."),
        ("reviewed_at", "datetime", "-", "Review timestamp."),
        ("uploaded_at", "timestamp", "-", "Upload timestamp."),
    ],
    "death_certificate_requests": [
        ("request_id", "int(11)", "PK", "Unique request ID."),
        ("record_id", "int(11)", "FK", "Approved death record being requested."),
        ("requester_user_id", "int(11)", "FK", "User who submitted the request."),
        ("recipient_email", "varchar(190)", "-", "Registry office email recipient."),
        ("purpose", "varchar(150)", "-", "Purpose of the request."),
        ("copies_requested", "int(11)", "-", "Number of certificate copies requested."),
        ("contact_number", "varchar(40)", "-", "Contact number of the requester."),
        ("remarks", "text", "-", "Additional notes for the request."),
        ("status", "varchar(30)", "-", "Current request status."),
        ("email_status", "varchar(30)", "-", "Email delivery status."),
        ("email_error", "text", "-", "Email error details if sending fails."),
        ("submitted_at", "timestamp", "-", "Request submission timestamp."),
        ("emailed_at", "datetime", "-", "Timestamp when email was sent."),
    ],
    "email_verification_tokens": [
        ("verification_id", "int(11)", "PK", "Unique verification token ID."),
        ("user_id", "int(11)", "FK", "Linked user."),
        ("token_hash", "char(64)", "UK", "Hashed email verification token."),
        ("expires_at", "datetime", "-", "Token expiration date and time."),
        ("used_at", "datetime", "-", "Timestamp when the token was used."),
        ("created_at", "timestamp", "-", "Token creation timestamp."),
    ],
    "password_reset_tokens": [
        ("reset_id", "int(11)", "PK", "Unique reset token ID."),
        ("user_id", "int(11)", "FK", "Linked user."),
        ("token_hash", "char(64)", "UK", "Hashed password reset token."),
        ("expires_at", "datetime", "-", "Token expiration date and time."),
        ("used_at", "datetime", "-", "Timestamp when the token was used."),
        ("created_at", "timestamp", "-", "Token creation timestamp."),
    ],
    "social_accounts": [
        ("social_account_id", "int(11)", "PK", "Unique social account link ID."),
        ("user_id", "int(11)", "FK", "Linked local user account."),
        ("provider", "varchar(30)", "UK composite", "Social login provider name."),
        ("provider_user_id", "varchar(191)", "UK composite", "Provider-side account ID."),
        ("provider_email", "varchar(191)", "-", "Email returned by the social provider."),
        ("created_at", "timestamp", "-", "Link creation timestamp."),
        ("last_login_at", "timestamp", "-", "Last provider login timestamp."),
    ],
    "notifications": [
        ("notification_id", "int(11)", "PK", "Unique notification ID."),
        ("user_id", "int(11)", "Reference", "Recipient user."),
        ("related_record_id", "int(11)", "Reference", "Related death record, if any."),
        ("title", "varchar(150)", "-", "Notification title."),
        ("message", "text", "-", "Notification body."),
        ("is_read", "tinyint(1)", "-", "Read flag."),
        ("created_at", "timestamp", "-", "Creation timestamp."),
    ],
    "record_activity_logs": [
        ("log_id", "int(11)", "PK", "Unique activity log ID."),
        ("related_record_id", "int(11)", "Reference", "Linked death record."),
        ("actor_user_id", "int(11)", "Reference", "User who performed the action."),
        ("affected_user_id", "int(11)", "Reference", "User affected by the action."),
        ("action_type", "varchar(50)", "-", "Type of logged action."),
        ("old_status", "varchar(30)", "-", "Previous record status."),
        ("new_status", "varchar(30)", "-", "New record status."),
        ("remarks", "text", "-", "Additional note for the activity."),
        ("details", "text", "-", "Description of the action performed."),
        ("created_at", "timestamp", "-", "Log creation timestamp."),
    ],
}


DATA_DICTIONARY = {
    "roles": [
        ("role_id", "int(11)", "No", "PK", "Numeric role identifier."),
        ("role_name", "varchar(50)", "No", "-", "Human-readable role name."),
    ],
    "users": [
        ("user_id", "int(11)", "No", "PK, auto increment", "Unique user identifier."),
        ("full_name", "varchar(100)", "No", "-", "Person's full name."),
        ("email", "varchar(120)", "No", "Unique", "Login email and contact email."),
        ("password", "varchar(255)", "No", "-", "Hashed password."),
        ("role_id", "int(11)", "No", "FK, default 3", "References roles.role_id."),
        ("status", "varchar(20)", "No", "default active", "Account state."),
        ("email_verified_at", "datetime", "Yes", "NULL", "Timestamp when email was verified."),
        ("created_at", "timestamp", "No", "current_timestamp()", "Account creation timestamp."),
    ],
    "death_records": [
        ("record_id", "int(11)", "No", "PK, auto increment", "Unique death record identifier."),
        ("tracking_code", "varchar(20)", "Yes", "Unique", "Public reference code."),
        ("deceased_name", "varchar(150)", "No", "-", "Name of the deceased person."),
        ("date_of_death", "date", "No", "-", "Date of death."),
        ("place_of_death", "varchar(150)", "No", "-", "Place where death occurred."),
        ("cause_of_death", "varchar(200)", "No", "-", "Recorded cause of death."),
        ("informant_name", "varchar(150)", "No", "-", "Name of the informant."),
        ("relationship", "varchar(50)", "No", "-", "Relationship of the informant."),
        ("applicant_user_id", "int(11)", "No", "FK", "Applicant user account."),
        ("created_by", "int(11)", "No", "FK", "Staff/admin who created the record."),
        ("status", "varchar(30)", "Yes", "default Pending", "Workflow status."),
        ("date_submitted", "timestamp", "No", "current_timestamp()", "Submission timestamp."),
        ("deleted_at", "datetime", "Yes", "NULL", "Soft-delete timestamp."),
        ("deleted_by", "int(11)", "Yes", "FK, NULL", "User who archived the record."),
    ],
    "record_documents": [
        ("document_id", "int(11)", "No", "PK, auto increment", "Unique uploaded document identifier."),
        ("record_id", "int(11)", "No", "FK", "Parent death record."),
        ("document_type", "varchar(80)", "No", "-", "Type of uploaded document."),
        ("original_file_name", "varchar(255)", "No", "-", "Original file name from upload."),
        ("stored_file_name", "varchar(255)", "No", "-", "Server-side generated file name."),
        ("file_path", "varchar(255)", "No", "-", "Relative storage path."),
        ("mime_type", "varchar(120)", "Yes", "NULL", "Detected MIME type."),
        ("file_size", "int(11)", "No", "default 0", "File size in bytes."),
        ("uploaded_by", "int(11)", "No", "FK", "User who uploaded the file."),
        ("review_status", "varchar(30)", "No", "default Pending Review", "Document review state."),
        ("review_notes", "text", "Yes", "NULL", "Reviewer comments."),
        ("reviewed_by", "int(11)", "Yes", "FK, NULL", "Reviewer account."),
        ("reviewed_at", "datetime", "Yes", "NULL", "Review timestamp."),
        ("uploaded_at", "timestamp", "No", "current_timestamp()", "Upload timestamp."),
    ],
    "death_certificate_requests": [
        ("request_id", "int(11)", "No", "PK, auto increment", "Unique certificate request identifier."),
        ("record_id", "int(11)", "No", "FK", "Approved death record being requested."),
        ("requester_user_id", "int(11)", "No", "FK", "User who submitted the request."),
        ("recipient_email", "varchar(190)", "No", "-", "Configured registry email address."),
        ("purpose", "varchar(150)", "No", "-", "Reason for requesting the certificate."),
        ("copies_requested", "int(11)", "No", "default 1", "Number of copies requested."),
        ("contact_number", "varchar(40)", "Yes", "NULL", "Optional contact number."),
        ("remarks", "text", "Yes", "NULL", "Optional notes."),
        ("status", "varchar(30)", "No", "default Submitted", "Request state."),
        ("email_status", "varchar(30)", "No", "default Pending", "Delivery state."),
        ("email_error", "text", "Yes", "NULL", "Email delivery error details."),
        ("submitted_at", "timestamp", "No", "current_timestamp()", "Request submission timestamp."),
        ("emailed_at", "datetime", "Yes", "NULL", "When the email was successfully sent."),
    ],
    "email_verification_tokens": [
        ("verification_id", "int(11)", "No", "PK, auto increment", "Unique verification token row."),
        ("user_id", "int(11)", "No", "FK", "Owner of the token."),
        ("token_hash", "char(64)", "No", "Unique", "SHA-256 hash of the raw token."),
        ("expires_at", "datetime", "No", "-", "Expiration timestamp."),
        ("used_at", "datetime", "Yes", "NULL", "Set when the token is consumed."),
        ("created_at", "timestamp", "No", "current_timestamp()", "Token creation timestamp."),
    ],
    "password_reset_tokens": [
        ("reset_id", "int(11)", "No", "PK, auto increment", "Unique reset token row."),
        ("user_id", "int(11)", "No", "FK", "Owner of the token."),
        ("token_hash", "char(64)", "No", "Unique", "SHA-256 hash of the raw reset token."),
        ("expires_at", "datetime", "No", "-", "Expiration timestamp."),
        ("used_at", "datetime", "Yes", "NULL", "Set when the token is consumed."),
        ("created_at", "timestamp", "No", "current_timestamp()", "Token creation timestamp."),
    ],
    "social_accounts": [
        ("social_account_id", "int(11)", "No", "PK, auto increment", "Unique linked social account identifier."),
        ("user_id", "int(11)", "No", "FK", "Local user account linked to the provider."),
        ("provider", "varchar(30)", "No", "Part of unique key", "Social auth provider name."),
        ("provider_user_id", "varchar(191)", "No", "Part of unique key", "Provider-side identifier."),
        ("provider_email", "varchar(191)", "Yes", "NULL", "Email returned by the provider."),
        ("created_at", "timestamp", "No", "current_timestamp()", "Link creation timestamp."),
        ("last_login_at", "timestamp", "Yes", "NULL", "Last login time via the provider."),
    ],
    "notifications": [
        ("notification_id", "int(11)", "No", "PK, auto increment", "Unique notification identifier."),
        ("user_id", "int(11)", "No", "Reference", "Recipient user account."),
        ("related_record_id", "int(11)", "Yes", "Reference", "Related death record."),
        ("title", "varchar(150)", "No", "-", "Notification headline."),
        ("message", "text", "No", "-", "Notification body."),
        ("is_read", "tinyint(1)", "No", "default 0", "Read flag."),
        ("created_at", "timestamp", "No", "current_timestamp()", "Notification creation timestamp."),
    ],
    "record_activity_logs": [
        ("log_id", "int(11)", "No", "PK, auto increment", "Unique log identifier."),
        ("related_record_id", "int(11)", "Yes", "Reference", "Death record involved in the event."),
        ("actor_user_id", "int(11)", "No", "Reference", "User who performed the action."),
        ("affected_user_id", "int(11)", "Yes", "Reference", "User affected by the event."),
        ("action_type", "varchar(50)", "No", "-", "Machine-friendly action label."),
        ("old_status", "varchar(30)", "Yes", "NULL", "Previous workflow status."),
        ("new_status", "varchar(30)", "Yes", "NULL", "New workflow status."),
        ("remarks", "text", "Yes", "NULL", "Optional note saved with the event."),
        ("details", "text", "Yes", "NULL", "Human-readable event description."),
        ("created_at", "timestamp", "No", "current_timestamp()", "Event timestamp."),
    ],
}


def box_dimensions(entity: dict) -> tuple[int, int]:
    width = 350
    height = 52 + len(entity["fields"]) * 24 + 16
    return width, height


def entity_map(entities: list[dict]):
    data = {}
    for entity in entities:
        width, height = box_dimensions(entity)
        data[entity["name"]] = {**entity, "width": width, "height": height}
    return data


def point_for(box: dict, side: str) -> tuple[int, int]:
    if side == "left":
        return box["x"], box["y"] + box["height"] // 2
    if side == "right":
        return box["x"] + box["width"], box["y"] + box["height"] // 2
    if side == "top":
        return box["x"] + box["width"] // 2, box["y"]
    return box["x"] + box["width"] // 2, box["y"] + box["height"]


def route_between(
    a: dict, b: dict, offset: int = 0
) -> tuple[tuple[int, int], tuple[int, int], tuple[int, int], tuple[int, int]]:
    if a["x"] + a["width"] < b["x"]:
        start = point_for(a, "right")
        end = point_for(b, "left")
        mid_x = (start[0] + end[0]) // 2
        return start, (mid_x, start[1] + offset), (mid_x, end[1] + offset), end
    if b["x"] + b["width"] < a["x"]:
        start = point_for(a, "left")
        end = point_for(b, "right")
        mid_x = (start[0] + end[0]) // 2
        return start, (mid_x, start[1] + offset), (mid_x, end[1] + offset), end
    if a["y"] < b["y"]:
        start = point_for(a, "bottom")
        end = point_for(b, "top")
        mid_y = (start[1] + end[1]) // 2
        return start, (start[0] + offset, mid_y), (end[0] + offset, mid_y), end
    start = point_for(a, "top")
    end = point_for(b, "bottom")
    mid_y = (start[1] + end[1]) // 2
    return start, (start[0] + offset, mid_y), (end[0] + offset, mid_y), end


def polyline_points(points: tuple[tuple[int, int], ...]) -> str:
    return " ".join(f"{x},{y}" for x, y in points)


def label_box_width(text: str) -> int:
    return max(112, len(text) * 7 + 20)


def build_svg(
    entities: list[dict],
    relations: list[tuple],
    title: str,
    subtitle: str,
    footer: str,
    width: int,
    height: int,
) -> str:
    boxes = entity_map(entities)
    canvas_width = width
    canvas_height = height
    parts = [
        '<?xml version="1.0" encoding="UTF-8"?>',
        f'<svg xmlns="http://www.w3.org/2000/svg" width="{canvas_width}" height="{canvas_height}" viewBox="0 0 {canvas_width} {canvas_height}">',
        '<style>',
        'text { font-family: Arial, Helvetica, sans-serif; fill: #1f2937; }',
        '.title { font-size: 14px; font-weight: 700; fill: #ffffff; }',
        '.field { font-size: 12px; }',
        '.label { font-size: 11px; font-weight: 700; fill: #0f172a; }',
        '.legend { font-size: 12px; }',
        '</style>',
        '<rect width="100%" height="100%" fill="#f8fafc"/>',
        f'<text x="40" y="28" style="font-size:22px;font-weight:700;fill:#0f172a;">{escape(title)}</text>',
        f'<text x="40" y="48" class="legend">{escape(subtitle)}</text>',
    ]

    for relation in relations:
        left, right, left_card, right_card, label = relation[:5]
        line_style = relation[5] if len(relation) >= 6 else "solid"
        offset = relation[6] if len(relation) >= 7 else 0
        a = boxes[left]
        b = boxes[right]
        pts = route_between(a, b, int(offset))
        start, mid1, mid2, end = pts
        dash = ' stroke-dasharray="8 6"' if line_style == "dashed" else ""
        parts.append(
            f'<polyline points="{polyline_points(pts)}" fill="none" stroke="#64748b" stroke-width="2"{dash}/>'
        )
        lx = (mid1[0] + mid2[0]) // 2
        ly = (mid1[1] + mid2[1]) // 2 - 6
        box_w = label_box_width(label)
        parts.append(f'<rect x="{lx - box_w // 2}" y="{ly - 13}" width="{box_w}" height="18" rx="6" fill="#f8fafc"/>')
        parts.append(f'<text x="{lx}" y="{ly}" class="label" text-anchor="middle">{escape(label)}</text>')
        parts.append(f'<text x="{start[0] + 6}" y="{start[1] - 6}" class="label">{escape(left_card)}</text>')
        parts.append(f'<text x="{end[0] - 28}" y="{end[1] - 6}" class="label">{escape(right_card)}</text>')

    for entity in entities:
        box = boxes[entity["name"]]
        x = box["x"]
        y = box["y"]
        box_width = box["width"]
        box_height = box["height"]
        title_height = 34
        parts.append(f'<rect x="{x}" y="{y}" width="{box_width}" height="{box_height}" rx="10" fill="#ffffff" stroke="#0f172a" stroke-width="1.5"/>')
        parts.append(f'<rect x="{x}" y="{y}" width="{box_width}" height="{title_height}" rx="10" fill="#0f766e"/>')
        parts.append(f'<rect x="{x}" y="{y + title_height - 10}" width="{box_width}" height="10" fill="#0f766e"/>')
        parts.append(f'<text x="{x + 14}" y="{y + 22}" class="title">{escape(entity["name"])}</text>')
        parts.append(f'<line x1="{x}" y1="{y + title_height}" x2="{x + box_width}" y2="{y + title_height}" stroke="#cbd5e1" stroke-width="1"/>')
        fy = y + 54
        for field in entity["fields"]:
            parts.append(f'<text x="{x + 14}" y="{fy}" class="field">{escape(field)}</text>')
            fy += 24

    parts.extend(
        [
            f'<rect x="40" y="{canvas_height - 60}" width="{canvas_width - 80}" height="34" rx="8" fill="#e2e8f0"/>',
            f'<text x="56" y="{canvas_height - 38}" class="legend">{escape(footer)}</text>',
            '</svg>',
        ]
    )
    return "\n".join(parts)


def html_table(rows: list[tuple[str, ...]], headers: tuple[str, ...]) -> str:
    head = "".join(f"<th>{escape(cell)}</th>" for cell in headers)
    body = []
    for row in rows:
        cells = "".join(f"<td>{escape(cell)}</td>" for cell in row)
        body.append(f"<tr>{cells}</tr>")
    return f"<table><thead><tr>{head}</tr></thead><tbody>{''.join(body)}</tbody></table>"


def build_database_design_html() -> str:
    sections = []
    for table_name, rows in TABLE_SUMMARY.items():
        sections.append(f"<h3>{escape(table_name)}</h3>")
        sections.append(html_table(rows, ("Field", "Type", "Key", "Description")))
    return f"""<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Memento Vitae Database Design</title>
  <style>
    body {{ font-family: Arial, Helvetica, sans-serif; margin: 32px; color: #1f2937; background: #f8fafc; }}
    h1, h2, h3 {{ color: #0f172a; }}
    .card {{ background: #ffffff; border: 1px solid #cbd5e1; border-radius: 14px; padding: 24px; margin-bottom: 24px; }}
    img {{ max-width: 100%; height: auto; border: 1px solid #cbd5e1; border-radius: 12px; background: #ffffff; }}
    table {{ width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 14px; }}
    th, td {{ border: 1px solid #cbd5e1; padding: 10px; text-align: left; vertical-align: top; }}
    th {{ background: #e2e8f0; }}
    .muted {{ color: #475569; }}
  </style>
</head>
<body>
  <div class="card">
    <h1>Memento Vitae Database Design</h1>
    <p class="muted">Prepared for the 6DWEB documentation requirement under Database Design, before the 6IMAN section.</p>
  </div>
  <div class="card">
    <h2>ERD</h2>
    <img src="ERD.svg" alt="Memento Vitae ERD">
    <p class="muted">This is the strict database ERD showing the core schema relationships for submission.</p>
  </div>
  <div class="card">
    <h2>Logical Reference Diagram</h2>
    <img src="ERD_LOGICAL_REFERENCES.svg" alt="Memento Vitae Logical References Diagram">
    <p class="muted">This supporting diagram shows app-level relationships used by the PHP code but not enforced as foreign keys in the SQL dump.</p>
  </div>
  <div class="card">
    <h2>Tables and Fields Summary</h2>
    {''.join(sections)}
  </div>
</body>
</html>
"""


def build_data_dictionary_html() -> str:
    sections = []
    for table_name, rows in DATA_DICTIONARY.items():
        sections.append(f"<h2>{escape(table_name)}</h2>")
        sections.append(html_table(rows, ("Column", "Type", "Null", "Key / Default", "Description")))
    return f"""<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Memento Vitae Data Dictionary</title>
  <style>
    body {{ font-family: Arial, Helvetica, sans-serif; margin: 32px; color: #1f2937; background: #f8fafc; }}
    h1, h2 {{ color: #0f172a; }}
    .card {{ background: #ffffff; border: 1px solid #cbd5e1; border-radius: 14px; padding: 24px; margin-bottom: 24px; }}
    table {{ width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 14px; }}
    th, td {{ border: 1px solid #cbd5e1; padding: 10px; text-align: left; vertical-align: top; }}
    th {{ background: #e2e8f0; }}
    .muted {{ color: #475569; }}
  </style>
</head>
<body>
  <div class="card">
    <h1>Memento Vitae Data Dictionary</h1>
    <p class="muted">Non-Markdown version generated from the project schema and application usage.</p>
  </div>
  {''.join(f'<div class="card">{section}</div>' for section in sections)}
</body>
</html>
"""


def main() -> None:
    strict_svg = build_svg(
        STRICT_ENTITIES,
        STRICT_RELATIONS,
        "Memento Vitae ERD",
        "Strict database ERD showing actual foreign-key relationships from the SQL schema.",
        "This is the recommended submission image for the 6DWEB Database Design section.",
        1320,
        1530,
    )
    logical_svg = build_svg(
        LOGICAL_ENTITIES,
        LOGICAL_RELATIONS,
        "Memento Vitae Logical References",
        "Supporting diagram for references used by the PHP application but not enforced as foreign keys.",
        "Dashed lines represent application-level references only.",
        1320,
        940,
    )
    (DOCS / "ERD.svg").write_text(strict_svg, encoding="utf-8")
    (DOCS / "ERD_LOGICAL_REFERENCES.svg").write_text(logical_svg, encoding="utf-8")
    (DOCS / "6DWEB_DATABASE_DESIGN.html").write_text(build_database_design_html(), encoding="utf-8")
    (DOCS / "DATA_DICTIONARY.html").write_text(build_data_dictionary_html(), encoding="utf-8")
    print("Generated:")
    print(DOCS / "ERD.svg")
    print(DOCS / "ERD_LOGICAL_REFERENCES.svg")
    print(DOCS / "6DWEB_DATABASE_DESIGN.html")
    print(DOCS / "DATA_DICTIONARY.html")


if __name__ == "__main__":
    main()
