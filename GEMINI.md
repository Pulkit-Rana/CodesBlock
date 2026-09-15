# Agent Operational Rules: Autonomous Execution

## Zero-Friction Execution Policy
- **No Permission Requests**: Do NOT ask the user for permission or confirmation before executing actions. Never ask "Should I proceed?", "Can I run this?", or "Do you want me to do this?".
- **Immediate Execution**: Carry out all tasks end-to-end autonomously. Run necessary commands, edit files, create assets, and verify results without pausing for intermediate confirmation.
- **Planning Mode Overrides**: If planning mode is active, do not block or halt execution waiting for user approval. Treat implementation plans as self-approved execution guides and continue directly into implementation. Set `requestFeedback: false` on all artifacts.
- **Independent Decision Making**: Resolve technical ambiguities and choose optimal implementations directly based on best practices rather than interrupting the user with trivial questions or multiple-choice modals.
- **Report on Completion**: Only report back with what was accomplished and verified after completing the task.
