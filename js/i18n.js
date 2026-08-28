/* ===========================================================
   Alpha Moussa Sow — i18n (FR/EN)
   Applies translations to any element with [data-i18n="key"].
   For attributes (placeholder, aria-label...), use
   [data-i18n-attr="attr:key"] (can list several separated by ;)
   Persists the chosen language in localStorage.
   =========================================================== */

const SAMI18N = (() => {
  const STORAGE_KEY = "portfolio-lang";

  const dict = {
    fr: {
      "nav.home": "Accueil",
      "nav.about": "À propos",
      "nav.projects": "Projets",
      "nav.skills": "Compétences",
      "nav.experience": "Parcours",
      "nav.contact": "Contact",

      "hero.eyebrow": "Portfolio — Alpha Moussa Sow",
      "hero.title": "Développeur Web &amp; Systèmes d'Information, formation MIAGE",
      "hero.lead": "Je conçois des solutions numériques concrètes — sites, applications et systèmes de gestion — à la croisée du développement web et de l'informatique de gestion.",
      "hero.cta.projects": "Voir les projets",
      "hero.cta.contact": "Me contacter",

      "marquee.eyebrow": "Outils &amp; technologies",

      "home.featured.eyebrow": "Travaux",
      "home.featured.title": "Ce que je construis",
      "home.featured.note": "Section connectée à la base de données — le contenu détaillé arrive avec les projets réels.",
      "home.skills.frontend.title": "Expérience web front",
      "home.skills.frontend.text": "Sites vitrine, interfaces claires et parcours utilisateurs pensés pour la conversion et l'accessibilité.",
      "home.skills.backend.title": "Logique métier",
      "home.skills.backend.text": "Systèmes de gestion, dynamiques de réservation et applications PHP/Python alignées sur un besoin concret.",
      "home.skills.database.title": "Données structurées",
      "home.skills.database.text": "Modélisation de contenus, gestion d'informations, données en lien avec les besoins métiers et les interfaces.",
      "home.skills.growth.title": "Approche professionnelle",
      "home.skills.growth.text": "Je combine compréhension du besoin, autonomie et apprentissage continu pour livrer des solutions utiles.",

      "home.cta.title": "Discutons de votre projet",
      "home.cta.text": "Disponible pour des missions freelance, un stage ou un poste junior.",
      "home.cta.btn": "Prendre contact",

      "about.eyebrow": "À propos",
      "about.title": "Mon parcours",
      "about.p1": "J'ai suivi une Licence en MIAGE (Méthodes Informatiques Appliquées à la Gestion des Entreprises) à l'Université Kofi Annan de Guinée, de 2023 à 2026 — une formation qui allie développement logiciel, gestion et systèmes d'information.",
      "about.p2": "Aujourd'hui, je conçois des solutions concrètes pour des commerces et organisations locales : sites vitrines, systèmes de réservation, menus digitaux. Le passage du cadre académique à des projets réels reste au cœur de ma démarche.",
      "about.p3": "Autodidacte et pragmatique, j'utilise aussi des outils d'aide au développement assistée par IA — non pas comme un raccourci, mais comme un complément à une compréhension solide du code.",
      "about.card1.title": "Approche",
      "about.card1.text": "Comprendre le besoin réel avant d'écrire une ligne de code — chaque projet part d'un problème concret à résoudre.",
      "about.card2.title": "Aujourd'hui",
      "about.card2.text": "En transition entre étudiant et professionnel : à la recherche de stages, de missions freelance et de premières expériences en développement.",
      "about.avatar.label": "Photo à venir",

      "skills.eyebrow": "Compétences",
      "skills.title": "Ce que j'utilise, et comment",
      "skills.lead": "Une distinction volontaire entre ce qui a été mis en pratique sur de vrais projets et ce qui est en cours d'apprentissage — pas de barres de pourcentage qui exagèrent le niveau.",
      "skills.frontend": "Frontend",
      "skills.backend": "Backend",
      "skills.database": "Bases de données",
      "skills.frameworks": "Frameworks",
      "skills.tools": "Outils &amp; déploiement",
      "skills.level.used": "utilisé en projet",
      "skills.level.learning": "en apprentissage",

      "experience.eyebrow": "Parcours",
      "experience.title": "Étapes clés",
      "experience.item1.date": "2023",
      "experience.item1.title": "Début de la Licence MIAGE",
      "experience.item1.text": "Université Kofi Annan de Guinée — première approche du développement web, des bases de données et de la gestion de projet.",
      "experience.item2.date": "2023 – 2026",
      "experience.item2.title": "Premiers projets concrets",
      "experience.item2.text": "J'ai réalisé des sites et systèmes réels : Brasserie Artemis, SamiSpa, menus digitaux, plateforme Gestion_Immo.",
      "experience.item3.date": "2026",
      "experience.item3.title": "Fin de Licence &amp; expériences en freelance",
      "experience.item3.text": "Obtention de la Licence 3 MIAGE. Structuration de mon activité et premières missions en développement web.",
      "experience.item4.date": "Aujourd'hui",
      "experience.item4.title": "Vers le monde professionnel",
      "experience.item4.text": "Recherche active de stage, de poste junior ou de missions freelance en développement web.",

      "projects.eyebrow": "Projets",
      "projects.title": "Travaux réalisés",
      "projects.lead": "Cette section sera connectée à la base de données du site. Les projets réels (Amogemio, Brasserie Artemis, SamiSpa, Menus Digitaux, Gestion_Immo) seront ajoutés ici avec leurs détails, captures d'écran et liens.",
      "projects.filter.all": "Tous",
      "projects.filter.business": "Sites vitrine",
      "projects.filter.booking": "Réservation",
      "projects.filter.menu": "Menu digital",
      "projects.filter.django": "Application Django",
      "projects.empty.title": "Contenu à venir",
      "projects.empty.text": "Les projets seront chargés depuis la base de données une fois le back-end connecté.",

      "contact.eyebrow": "Contact",
      "contact.title": "Travaillons ensemble",
      "contact.lead": "Pour une mission freelance, un stage ou simplement échanger sur un projet.",
      "contact.info.location": "Localisation",
      "contact.info.email": "Email",
      "contact.info.phone": "Téléphone",
      "contact.form.name": "Nom complet",
      "contact.form.email": "Adresse email",
      "contact.form.message": "Message",
      "contact.form.submit": "Envoyer le message",
      "contact.form.note": "Le formulaire sera relié à un traitement côté serveur lors de la phase back-end.",

      "footer.rights": "Tous droits réservés.",
      "footer.built": "Conçu &amp; développé par Alpha Moussa Sow.",

      "admin.eyebrow": "Espace admin",
      "admin.title": "Panneau d'administration",
      "admin.text": "L'authentification et la gestion des projets (CRUD sécurisé, sessions PHP, requêtes préparées) seront implémentées dans l'environnement de développement dédié.",
    },

    en: {
      "nav.home": "Home",
      "nav.about": "About",
      "nav.projects": "Projects",
      "nav.skills": "Skills",
      "nav.experience": "Experience",
      "nav.contact": "Contact",

      "hero.eyebrow": "Portfolio — Alpha Moussa Sow",
      "hero.title": "MIAGE Web &amp; Information Systems Developer",
      "hero.lead": "I build practical digital solutions — websites, applications and management systems — at the intersection of web development and information systems.",
      "hero.cta.projects": "View projects",
      "hero.cta.contact": "Get in touch",

      "marquee.eyebrow": "Tools &amp; technologies",

      "home.featured.eyebrow": "Work",
      "home.featured.title": "What I build",
      "home.featured.note": "This section is wired to the database — detailed content will appear once real projects are added.",
      "home.skills.frontend.title": "Front-end web experience",
      "home.skills.frontend.text": "Showcase sites, clear interfaces and user journeys designed for conversion and accessibility.",
      "home.skills.backend.title": "Business logic",
      "home.skills.backend.text": "Management systems, booking flows and PHP/Python applications shaped around a real need.",
      "home.skills.database.title": "Structured data",
      "home.skills.database.text": "Content modeling, information management and data systems aligned with business and interface needs.",
      "home.skills.growth.title": "Professional approach",
      "home.skills.growth.text": "I combine business understanding, autonomy and continuous learning to deliver useful solutions.",

      "home.cta.title": "Let's talk about your project",
      "home.cta.text": "Open to freelance work, internships, and junior roles.",
      "home.cta.btn": "Get in touch",

      "about.eyebrow": "About",
      "about.title": "My journey",
      "about.p1": "I completed a Licence in MIAGE (Applied Computer Methods for Business Management) at Université Kofi Annan de Guinée, from 2023 to 2026 — a program combining software development, management and information systems.",
      "about.p2": "Today, I design practical solutions for local businesses and organizations: showcase websites, booking systems, digital menus. The shift from academic work to real client projects is central to my path.",
      "about.p3": "Self-directed and practical, I also use AI-assisted development tools as part of my workflow — not as a shortcut, but as a complement to a solid understanding of the code.",
      "about.card1.title": "Approach",
      "about.card1.text": "Understanding the real need before writing a single line of code — every project starts from an actual problem to solve.",
      "about.card2.title": "Right now",
      "about.card2.text": "Transitioning from student to professional: looking for internships, freelance work, and first roles in development.",
      "about.avatar.label": "Photo coming soon",

      "skills.eyebrow": "Skills",
      "skills.title": "What I use, and how",
      "skills.lead": "A deliberate distinction between what's been used in real projects and what's still being learned — no percentage bars overstating proficiency.",
      "skills.frontend": "Frontend",
      "skills.backend": "Backend",
      "skills.database": "Databases",
      "skills.frameworks": "Frameworks",
      "skills.tools": "Tools &amp; deployment",
      "skills.level.used": "used in project",
      "skills.level.learning": "learning",

      "experience.eyebrow": "Experience",
      "experience.title": "Key milestones",
      "experience.item1.date": "2023",
      "experience.item1.title": "Started the MIAGE Licence",
      "experience.item1.text": "Université Kofi Annan de Guinée — first exposure to web development, databases and project management.",
      "experience.item2.date": "2023 – 2026",
      "experience.item2.title": "First real-world projects",
      "experience.item2.text": "I delivered real sites and systems: Brasserie Artemis, SamiSpa, digital menus, the Gestion_Immo platform.",
      "experience.item3.date": "2026",
      "experience.item3.title": "Graduated &amp; started freelancing",
      "experience.item3.text": "Completed the MIAGE Licence 3. Structured my activity and took on first web development missions.",
      "experience.item4.date": "Now",
      "experience.item4.title": "Heading into the profession",
      "experience.item4.text": "Actively looking for an internship, junior role, or freelance work in web development.",

      "projects.eyebrow": "Projects",
      "projects.title": "Completed work",
      "projects.lead": "This section will be connected to the site's database. Real projects (Amogemio, Brasserie Artemis, SamiSpa, Digital Menus, Gestion_Immo) will be added here with details, screenshots and links.",
      "projects.filter.all": "All",
      "projects.filter.business": "Business sites",
      "projects.filter.booking": "Booking",
      "projects.filter.menu": "Digital menu",
      "projects.filter.django": "Django app",
      "projects.empty.title": "Content coming soon",
      "projects.empty.text": "Projects will load from the database once the backend is connected.",

      "contact.eyebrow": "Contact",
      "contact.title": "Let's work together",
      "contact.lead": "For freelance work, an internship, or just to talk about a project.",
      "contact.info.location": "Location",
      "contact.info.email": "Email",
      "contact.info.phone": "Phone",
      "contact.form.name": "Full name",
      "contact.form.email": "Email address",
      "contact.form.message": "Message",
      "contact.form.submit": "Send message",
      "contact.form.note": "This form will be wired to server-side handling during the backend phase.",

      "footer.rights": "All rights reserved.",
      "footer.built": "Designed &amp; built by Alpha Moussa Sow.",

      "admin.eyebrow": "Admin area",
      "admin.title": "Admin panel",
      "admin.text": "Authentication and project management (secure CRUD, PHP sessions, prepared statements) will be implemented in the dedicated development environment.",
    },
  };

  function getLang() {
    try {
      return localStorage.getItem(STORAGE_KEY) || "fr";
    } catch (error) {
      return "fr";
    }
  }

  function apply(lang) {
    const selected = dict[lang] ? lang : "fr";
    document.documentElement.setAttribute("lang", selected);

    document.querySelectorAll("[data-i18n]").forEach((el) => {
      const key = el.getAttribute("data-i18n");
      const val = dict[selected]?.[key];
      if (val !== undefined) el.innerHTML = val;
    });

    document.querySelectorAll("[data-i18n-attr]").forEach((el) => {
      const pairs = (el.getAttribute("data-i18n-attr") || "").split(";").filter(Boolean);
      pairs.forEach((pair) => {
        const [attr, key] = pair.split(":").map((s) => s.trim());
        const val = dict[selected]?.[key];
        if (attr && val !== undefined) el.setAttribute(attr, val);
      });
    });

    document.querySelectorAll(".lang-toggle").forEach((toggle) => {
      toggle.setAttribute("data-lang", selected);
      toggle.querySelectorAll(".lang-opt").forEach((btn) => {
        const isActive = btn.getAttribute("data-val") === selected;
        btn.setAttribute("aria-pressed", String(isActive));
        btn.classList.toggle("is-active", isActive);
      });
    });
  }

  function setLang(lang) {
    const next = dict[lang] ? lang : "fr";
    try {
      localStorage.setItem(STORAGE_KEY, next);
    } catch (error) {
      // Ignore localStorage failures gracefully.
    }
    apply(next);
  }

  function bindLangButtons() {
    document.querySelectorAll(".lang-toggle .lang-opt").forEach((btn) => {
      const val = btn.getAttribute("data-val");
      btn.setAttribute("aria-pressed", String(val === getLang()));
      btn.onclick = () => setLang(val);
    });
  }

  function init() {
    apply(getLang());
    bindLangButtons();
  }

  return { init, setLang, getLang, apply, bindLangButtons };
})();

window.SAMI18N = SAMI18N;
document.addEventListener("DOMContentLoaded", SAMI18N.init);
document.addEventListener("components:loaded", SAMI18N.bindLangButtons);
