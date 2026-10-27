# .agents/

Agent-agnostic configuration for `ns_t3ab` following [AGENTS.md](https://agents.md) and [Agent Skills](https://agentskills.io) conventions.

## Layout

- `skills/` — Shared skills (committed). Each directory contains `SKILL.md`.
- `skills-local/` — Private skills (gitignored), if used.
- Symlink helpers into `.claude/skills/` / `.cursor/skills/` only when skills exist (mirror `ns_t3af` if/when adding skills).

## Adding a skill

1. Create `.agents/skills/<name>/SKILL.md` with `name` + `description` frontmatter.
2. Link into agent skill dirs if the host tooling expects it.
3. Commit the skill directory (and any symlinks).

## Rules

- Edit skills in `.agents/skills/`, never in `.claude/skills/` or `.cursor/skills/` (those are symlinks when present).
- Edit agent context in `context/` and routers in `AGENTS.md`.
- Keep feature summaries short in `context/features/*.md`; put deep design notes only when a feature needs them.
