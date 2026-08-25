# HANDOFF — SamInnova Portfolio Site

Read this first. It replaces having to re-explain the project in a new
environment. Written so an AI assistant or a human picking this up in
Cursor/Windsurf knows exactly what exists, why, and what's left.

---

## 1. Who this is for

- **Name:** Alpha Moussa Sow
- **Activity name:** SamInnova (his real working name — never "WebWizard",
  that's an old/outdated name)
- **Status:** Completed a Licence 3 in MIAGE (Méthodes Informatiques
  Appliquées à la Gestion des Entreprises) at Université Kofi Annan de
  Guinée, 2023–2026. Transitioning from student to professional — targeting
  internships, junior dev roles, and freelance/client work.
- **Positioning line:** "MIAGE Web & Information Systems Developer" /
  "Développeur Web & Systèmes d'Information, formation MIAGE"
- **Narrative arc:** Student → practical builder → independent developer →
  emerging professional. NOT "student with only academic assignments."
- **Two audiences at once:** recruiters/companies hiring interns or juniors,
  and potential freelance/business clients.

### ⚠️ Branding decision — read before touching copy
SamInnova is real (it's his actual project/activity name, not invented) but
**it is not an officially registered business/établissement**. Per the
person's explicit instruction, marketing-style lines that presented it like
an established company brand were softened to lead with **"développeur
freelance" / "freelance developer"** instead:
- Hero eyebrow: "Portfolio — Développeur freelance" (was "Portfolio — SamInnova")
- Footer credit line: "Conçu & développé par un développeur freelance." (was "...par SamInnova.")

**What was deliberately left alone:** the nav wordmark/logo ("Sam<span>Innova</span>")
and the narrative content on About/Experience pages that factually describes
him operating under the SamInnova name — that's real biography, not
brand-inflation. **This is a judgment call, not a hard rule** — if the person
wants the logo/name pulled back further (or restored), that's a quick edit:
search `SamInnova` across the codebase, it now only appears in the nav
wordmark, page `<title>` tags, meta descriptions, and About/Experience prose.

### Confirmed real projects (the ONLY ones allowed in the Projects section)
| Project | Type | Status |
|---|---|---|
| Amogemio Website | Business site | Almost finished / near completion |
| Brasserie Artemis | Business/brewery website | Real, completed |
| SamiSpa | Booking/reservation system | Real, completed |
| Digital Menus | Digital menu solutions for restaurants | Real, completed |
| Gestion_Immo | Django/Python real-estate rental platform | Real, academic/practical project |

**Never** show as portfolio work: old CV filler like "Plateforme
Immobilière & Réservation," "Application de Gestion de Plannings,"
"Plateforme E-Learning Intégrée" — these are outdated, not confirmed real work.

**Never show as completed:** University Games / Jeux Universitaires and
Yame (dating app) — concept stage only, university-games/dating apps for
Guinea. Only allowed in a clearly separate "Upcoming / Currently Building"
section, never mixed with finished work, and only if explicitly requested.

### Contact info (real, verified — use only this)
- Location: Conakry, Guinée
- Email: 22nysam@gmail.com
- Phone: +224 612522199

### Technical profile
- **Core:** HTML, CSS, JavaScript, Bootstrap, PHP, SQL, MySQL, Python
- **Frameworks:** Django (used in project), Laravel (learning — don't
  overstate)
- **Tools/deployment:** Git/GitHub, VS Code, Netlify, AlwaysData, FileZilla,
  WinSCP, AI-assisted dev tools (fine to acknowledge as part of the
  workflow, not a replacement for understanding the code)

### Hard content rules (non-negotiable)
- Never invent: projects, clients, employers, awards, certifications,
  GitHub repos, results/metrics, user counts, revenue, technologies not
  actually used, testimonials, or "expert" claims without evidence.
- Never present an unfinished project as complete.
- If real project info (screenshots, links, descriptions) isn't available
  yet: use clearly generic placeholder copy ("Description à venir" /
  "Description coming soon") — never fabricate specifics even as a
  placeholder.
- If information is missing, ask for it or leave it out. Real project
  details should come from inspecting the actual site/repo, not memory.

---

## 2. Tech stack (locked)

| Layer | Technology |
|---|---|
| Frontend structure | HTML5 |
| Styling | CSS3 (custom, no heavy framework) |
| Interactivity/animation | Vanilla JavaScript |
| Projects section (dynamic) | PHP |
| Database | MySQL |
| Admin panel | PHP + MySQL, session-based auth |
| Hosting | AlwaysData |
| Deployment | FileZilla / WinSCP |
| Version control | Git / GitHub |

**Static pages** (plain HTML/CSS/JS, no database): Home, About, Skills,
Experience, Contact.
**Dynamic** (PHP + MySQL): Projects section + everything the admin panel edits.

**Language:** Bilingual FR/EN. Toggle in the nav, persists via
`localStorage`. All static content needs both languages.

---

## 3. Design system (locked — reuse exactly, don't reinvent per page)

Implemented as CSS custom properties in `css/tokens.css`. Two themes now
live side by side:

**Dark (default):**
```
--bg: #0A0F1C        --surface: #121A2C     --surface-2: #182238
--line: #24304A       --text: #E9EDF6        --text-dim: #8D97AE
--accent: #E8A33D     --accent-dim: #B9822E
```

**Light** (`[data-theme="light"]` on `<html>`, toggled + persisted like the
language switch):
```
--bg: #F5F3EE        --surface: #FFFFFF     --surface-2: #ECE8DF
--line: #DCD5C6       --text: #171A21        --text-dim: #5C6270
--accent: #C97B1F     --accent-dim: #A5651A
```
Light-theme hex values are a first pass — contrast has not had a full WCAG
AA pass yet in light mode specifically (dark mode was designed against the
spec's palette; light mode was derived to match). Worth auditing before ship.

**Typography:** Space Grotesk (display), Inter (body), JetBrains Mono
(labels/eyebrows/mono accents) — loaded via Google Fonts CDN in every
page's `<head>`.

**Signature visual motif:** animated SVG "node/schema" graphic (dots
connected by lines) in the hero — draws in on load, nodes float gently, and
now tilts subtly on cursor move (see §5). Ties to the MIAGE/information-
systems identity.

**Motion requirements:** scroll-triggered reveals via `IntersectionObserver`
(fade + slide, staggered), infinite auto-scrolling tech marquee (devicon
CDN icons, pauses on hover), hover lift/glow on cards, all of it disabled
under `prefers-reduced-motion`.

---

## 4. Site structure as it exists right now

```
saminnova/
├── index.html            Home — hero, tech marquee, featured project
│                          skeletons, CTA banner
├── about.html             About — MIAGE journey, transition to SamInnova
├── skills.html            Skills — grouped by Frontend/Backend/DB/
│                          Frameworks/Tools, icon chips, "used in project"
│                          vs "learning" labels (not percentage bars)
├── experience.html        Experience — animated vertical timeline
├── contact.html            Contact — info cards + form (client-side only)
├── projects/
│   └── index.html         Projects — filter tabs (visual only so far) +
│                          ONE empty-state placeholder card. No fabricated
│                          project data — see HTML comment marking where
│                          the PHP loop goes.
├── admin/
│   └── index.html         Placeholder shell only. HTML comment lists the
│                          full admin spec (see §7).
├── css/
│   ├── tokens.css         Design tokens: color (dark + light), type scale,
│                          spacing, motion durations
│   ├── base.css           Reset + base element styles
│   ├── components.css     Nav, buttons, cards (+ cursor glow), marquee,
│                          forms, timeline, theme-toggle, lang-toggle
│   └── pages.css          Page-specific layout: hero (+ load-in animation,
│                          cursor tilt), about grid, skills grid, contact
├── js/
│   ├── i18n.js             FR/EN dictionary + toggle, persists to
│                          localStorage under `saminnova-lang`
│   ├── theme.js            Dark/light toggle, persists under
│                          `saminnova-theme`, mirrors the i18n pattern
│   └── main.js             Nav toggle, scroll-reveal, marquee duplication,
│                          filter tabs, contact form validation, cursor-
│                          glow on cards, hero parallax tilt
└── README.md               Original structure notes (this file supersedes
                            it for context/progress — README stays as a
                            quick file-map reference)
```

---

## 5. What's been built (progress log)

**Session 1 — static skeleton:**
- Full design token system implemented as CSS variables
- All 6 static pages built with real bilingual copy (not lorem ipsum),
  matching the content rules in §1
- Bilingual FR/EN via `data-i18n` / `data-i18n-attr`, toggle in nav,
  persisted to `localStorage`
- Animated SVG node/schema hero graphic (stroke-draw-in + floating nodes)
- Scroll-triggered reveals (`IntersectionObserver`, staggered via
  `data-reveal-delay`)
- Infinite tech logo marquee (devicon CDN), pauses on hover
- Projects page built as an honest empty state — filter tabs are visual
  only, one placeholder card, HTML comment marking the PHP insertion point
- Admin page is a placeholder shell with the spec written into an HTML
  comment — no auth/logic
- Accessibility pass: semantic HTML, skip link, visible focus states, alt
  text, labeled form fields with error slots, responsive nav (hamburger
  under 860px), `prefers-reduced-motion` respected everywhere

**Session 2 — this round:**
- Softened "SamInnova" as a company-style brand in marketing copy (hero
  eyebrow, footer credit line) to "développeur freelance" framing — see the
  branding decision box in §1 for exactly what changed and what didn't
- Added a dark/light theme toggle (`js/theme.js`), same UX pattern as the
  language toggle, persisted separately, defaults to dark
- Added more motion/"life" to the pages:
  - Hero copy now does a staggered fade-up load-in (eyebrow → h1 → lead →
    buttons), independent of scroll position
  - Hero SVG graphic tilts subtly on cursor movement (`perspective` +
    `rotateX/rotateY`, resets on mouse leave)
  - Cards (project placeholders, about cards, contact cards) now have a
    cursor-following radial glow on hover, layered on top of the existing
    lift + border-glow
  - All of the above fully gated behind `prefers-reduced-motion`

---

## 6. What's next (in spec order)

The original 11-step build sequence from the build spec, with status:

1. ✅ Static HTML/CSS structure, all pages, mobile-first
2. ✅ Visual identity/base styles
3. ✅ Animation system (scroll-reveal, marquee, transitions) — plus the
   extra motion added in session 2
4. ✅ Bilingual FR/EN toggle system
5. ⬜ **MySQL schema + PHP backend for Projects** — schema is fully spec'd
   in §7, not yet implemented
6. ⬜ **Public Projects page pulling from DB** — replace the placeholder
   card in `projects/index.html` with a PHP loop; wire the filter tabs to
   actually filter by `category`
7. ⬜ **Admin panel: auth, dashboard, CRUD** — see §7 for the full spec;
   `admin/index.html` is a shell only
8. ⬜ **Contact form wiring** — currently client-side validation only
   (`js/main.js`), no server-side handler
9. ⬜ **Accessibility pass** — a first pass is already in (see §5), but
   worth a dedicated audit once dynamic content exists, and a WCAG AA
   contrast check on the new light theme specifically
10. ⬜ **Performance pass** — image optimization, lazy loading, minification
11. ⬜ **Deploy to AlwaysData**

### Also worth doing, not in the original spec
- Real content for the 5 confirmed projects (screenshots, descriptions,
  live/GitHub links) — must come from inspecting the actual
  sites/repos, never invented
- A headshot/photo for the About page (`avatar-frame` placeholder is
  waiting for it)
- Page `<title>` tags don't currently update on language toggle — only
  `data-i18n` elements do. Minor, but worth fixing if it bugs you
- Light-theme contrast audit (see §3 note)

---

## 7. Admin panel spec (for step 7)

**Scope: Projects only.** Nothing else on the site is admin-editable.

### `projects` table
```
id (PK)
title_fr, title_en
category (business site / booking system / digital menu / django app / other)
description_fr, description_en
problem_fr, problem_en
solution_fr, solution_en
tech_stack (comma-separated or JSON)
role
screenshot_url
live_url (nullable)
github_url (nullable)
status (completed / near-completion / in-progress) -- must reflect true status
display_order
created_at, updated_at
```

### `admin_users` table
```
id (PK)
username
password_hash
last_login
```

### Features
- Secure login: `password_hash()` / `password_verify()`, PHP sessions,
  CSRF tokens, rate-limiting/lockout on failed logins
- Dashboard listing all projects with edit/delete
- Add/edit form: bilingual fields, image upload with validation, status
  dropdown
- Delete with confirmation
- Logout
- All `/admin` routes require a valid session
- **All DB queries via prepared statements — no raw SQL concatenation,
  ever**

Reuse `tokens.css` / `components.css` / `pages.css` for the admin UI —
don't design it from scratch.

---

## 8. Accessibility requirements (ongoing)

Semantic HTML, proper heading hierarchy, alt text on all images, WCAG AA
contrast, visible keyboard focus states, logical tab order,
`prefers-reduced-motion` fallback, associated form labels/errors,
responsive mobile/tablet/desktop. Already implemented for the static
skeleton; re-verify once dynamic content and the admin panel land.
