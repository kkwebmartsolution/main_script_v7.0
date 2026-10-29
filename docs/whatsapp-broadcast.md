# WhatsApp template broadcasts

Open **Broadcasts → WhatsApp Template Message Send**. Access uses the existing `sendsmsmail/is_add` permission and `bulk_sms_and_email` module.

1. Create a template in the WhatsApp provider account and obtain approval. The example fee reminder is prefilled in the page's template editor; it is not automatically approved or sent for approval.
2. Save the exact approved name, language (`en`, `en_US`, etc.) and body in the page. Map each unique numbered variable to a database field. Repeated `{{2}}` uses the same value and is sent once in the parameter array.
3. For the example, map `{{1}}` to student name, `{{2}}` to pending amount, `{{3}}` to due date and `{{4}}` to school name. Keep the fee-reminder checkbox selected.
4. Select class, all or particular students, parent/student phone, and payment due date. Preview before sending. Keep the page open while sending; interrupted processing can be resumed in the same login session. Remaining messages can be cancelled while paused.

The due date is supplied for this broadcast only. Pending fees use current-session enrollment allocations, carried-forward dues and transport charges, less payments/discounts; fines are excluded. Zero-balance fee reminders and invalid/missing phone numbers are skipped. Branch and academic-session boundaries are validated on the server.

The local catalogue is `whatsapp_broadcast_templates`, automatically created on first authorized access. It uses utf8mb4 for emoji. Existing School Settings API URL/key are required. Templates requiring media, header parameters, authentication/OTP or dynamic buttons are outside this page's supported body-only format. Static headers/footers/buttons are supplied by the approved provider template. The preview shows the body only.

Messages use the existing `/messages` sender with the saved language and positional text parameters. Provider acceptance requires a message ID; HTTP 200 alone is insufficient. Per-message requests/responses remain in `whatsapp_queue`. Failed/unconfirmed requests are not retried automatically, since a timeout may occur after provider acceptance. Acceptance does not confirm delivery.

The configured provider's `/templates` and `/message-templates` endpoints returned HTTP 404 during implementation; the catalogue is manually registered, not synchronized. No live broadcast was sent during validation.

Validation command (all HTTP functions mocked, no real messages/database writes):

```powershell
C:/xampp/php/php.exe -d disable_functions=curl_init,curl_setopt,curl_exec,curl_getinfo,curl_error,curl_close tests/whatsapp_broadcast_test.php
```
