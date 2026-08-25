# SamInnova Portfolio — Frontend Skeleton

Static frontend skeleton for Alpha Moussa Sow's portfolio, built to spec from
`01-PROJECT-CONTEXT` and `02-BUILD-SPEC`. Built here for fast visual iteration;
hand off to Cursor/Windsurf for the PHP + MySQL backend (Projects section +
admin panel).

## Structure

```
saminnova/
├── index.html            Home
├── about.html             About
├── skills.html            Skills
├── experience.html        Experience / journey (timeline)
├── contact.html            Contact (form is client-side only for now)
├── projects/
│   └── index.html         Projects — EMPTY STATE ONLY, no fabricated cards.
│                          Wire this to the `projects` MySQL table next.
├── admin/
│   └── index.html         Placeholder shell only. Real auth/CRUD (PHP
│                          sessions, CSRF, prepared statements) belongs here.
├── css/
│   ├── tokens.css         Design tokens — colors, type scale, spacing, motion
│   ├── base.css           Reset + base element styles
│   ├── components.css     Nav, buttons, cards, marquee, forms, timeline...
│   └── pages.css          Page-specific layout (hero, about, skills grid...)
├── js/
│   ├── i18n.js             FR/EN dictionary + toggle, persists to localStorage
│   └── main.js             Nav toggle, scroll-reveal, marquee loop, filter tabs
└── assets/img/            Empty — placeholders in use everywhere for now
```

## What's already done

- Full design system implemented as CSS custom properties (`css/tokens.css`)
- Bilingual FR/EN via `data-i18n` attributes, persisted in `localStorage`
- Animated SVG "node/schema" hero graphic (draws in on load, nodes float)
- Scroll-triggered reveals via `IntersectionObserver`
- Infinite tech logo marquee (devicon CDN), pauses on hover
- Accessible: semantic HTML, skip link, visible focus states, alt text,
  labeled form fields, `prefers-reduced-motion` respected throughout
- Responsive down to mobile (hamburger nav under 860px)

## What's intentionally NOT built yet (per build sequence, steps 5–11)

- MySQL schema + PHP backend for the `projects` table
- Public Projects page pulling real rows (currently a labeled empty state —
  do not add hardcoded project cards; project content rules forbid
  fabricated project data)
- Admin panel auth (login, sessions, CSRF, rate-limiting) and CRUD
- Contact form server-side handling (currently front-end validation only)
- Image optimization / lazy loading / minification pass
- Deployment to AlwaysData

## Notes for whoever continues in Cursor/Windsurf

- Reuse `tokens.css` / `components.css` / `pages.css` as-is for anything new
  (admin dashboard, project case-study view) — don't reinvent styles per page.
- `projects/index.html` has an HTML comment marking exactly where the PHP loop
  should render cards from the database.
- `admin/index.html` has an HTML comment listing the admin spec (login,
  dashboard, project form) from the build spec's Admin panel section.
- Real project content (Amogemio, Brasserie Artemis, SamiSpa, Digital Menus,
  Gestion_Immo) must come from inspecting the actual sites/repos — never
  invent descriptions, metrics, or client names.
