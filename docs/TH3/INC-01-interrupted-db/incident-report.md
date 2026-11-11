# Incident Report — INC-01: Database connection unavailable

> Template for a controlled local/test exercise. Fill the timeline and evidence only after performing the exercise.

## Summary
- Environment: local/test only
- Severity: to be assigned from observed impact
- Start/end time: [record actual timestamps]
- Customer impact: [describe observed impact; do not assume]

## Detection and evidence
- Detection: GET /health returned HTTP 503 when the test MySQL service was unavailable.
- Evidence: [attach sanitized response/log screenshot under docs/TH3/evidence/]
- Sensitive data: do not include credentials, tokens, personal data or payment information.

## Timeline
| Time | Event | Evidence |
|---|---|---|
| [actual time] | Test DB made unavailable | [file/link] |
| [actual time] | Health check returned 503 | [file/link] |
| [actual time] | DB/config restored | [file/link] |
| [actual time] | Health and smoke tests passed | [file/link] |

## Root cause / 5 Whys
1. Why did health check fail? The application could not execute SELECT 1.
2. Why could it not connect? [record observed reason]
3. Why was the condition not prevented/detected earlier? [record]
4. What process/control was missing? [record]
5. What systemic action prevents recurrence? [record]

## Recovery and rollback
[Record the exact recovery/rollback action and commit/tag actually used.]

## Metrics
- MTTD: [calculate from actual timestamps]
- MTTR: [calculate from actual timestamps]
- RPO/RTO: [use measured backup/restore results]

## Follow-up actions
- [ ] Add/verify DB availability alert.
- [ ] Verify environment variables before deployment.
- [ ] Run restore drill and record timings.
- [ ] Add regression test for the failure scenario.
