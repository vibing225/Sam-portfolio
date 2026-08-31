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
      "hero.title": "Développeur web &amp; systèmes d'information, orienté solutions concrètes",
      "hero.lead": "Je conçois des sites vitrines, applications web et outils de gestion qui aident les entreprises à vendre, organiser et mieux servir leurs clients.",
      "hero.cta.projects": "Voir les projets",
      "hero.cta.contact": "Me contacter",

      "marquee.eyebrow": "Outils &amp; technologies",

      "home.featured.eyebrow": "Travaux",
      "home.featured.title": "Ce que je construis",
      "home.featured.note": "Sites vitrines, outils de gestion, applications web et solutions de réservation pensées pour des besoins concrets.",
      "home.skills.frontend.title": "Expérience web front",
      "home.skills.frontend.text": "Sites vitrines, interfaces claires et parcours clients pensés pour la conversion, la lisibilité et l'accessibilité.",
      "home.skills.backend.title": "Logique métier",
      "home.skills.backend.text": "Applications web, systèmes de gestion, outils de réservation et solutions PHP/Python conçues autour d'un besoin réel.",
      "home.skills.database.title": "Données & organisation",
      "home.skills.database.text": "Modélisation de contenus, gestion de données et mise en place d’outils structurés pour soutenir les activités et les interfaces.",
      "home.skills.growth.title": "Approche professionnelle",
      "home.skills.growth.text": "Je combine compréhension du besoin, autonomie et apprentissage continu pour livrer des solutions utiles et durables.",

      "home.cta.title": "Besoin d'un site ou d'un outil web ?",
      "home.cta.text": "Disponible pour des missions freelance, des stages ou des postes junior en développement web.",
      "home.cta.btn": "Prendre contact",

      "about.eyebrow": "À propos",
      "about.title": "Mon parcours",
      "about.p1": "J'ai suivi une Licence MIAGE à l'Université Kofi Annan de Guinée, de 2023 à 2026 — une formation qui mêle développement logiciel, gestion et systèmes d'information.",
      "about.p2": "Aujourd'hui, je conçois des sites vitrines, outils de gestion, systèmes de réservation et solutions web utiles pour des entreprises et des structures locales. Ce qui me motive, c'est la création de produits qui répondent à un vrai besoin métier.",
      "about.p3": "Autodidacte et pragmatique, j'utilise aussi des outils d'aide au développement assistée par IA comme complément à une compréhension solide du code et à une logique de projet claire.",
      "about.card1.title": "Approche",
      "about.card1.text": "Comprendre le besoin réel avant de coder — chaque projet part d'un problème concret à résoudre avec une solution simple, claire et utile.",
      "about.card2.title": "Aujourd'hui",
      "about.card2.text": "En transition entre étudiant et professionnel, je suis à la recherche de stages, de missions freelance et de premières expériences en développement web.",
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
      "experience.item2.text": "J'ai réalisé des sites et systèmes réels : Brasserie Artemis, SamiSpa, menus digitaux et la plateforme Gestion_Immo.",
      "experience.item3.date": "2026",
      "experience.item3.title": "Fin de Licence &amp; premiers projets freelance",
      "experience.item3.text": "Obtention de la Licence 3 MIAGE, structuration de mon activité et premiers projets en développement web.",
      "experience.item4.date": "Aujourd'hui",
      "experience.item4.title": "Vers le monde professionnel",
      "experience.item4.text": "Je cherche activement un stage, une première expérience junior ou des missions freelance autour du web.",

      "projects.eyebrow": "Projets",
      "projects.title": "Travaux réalisés",
      "projects.lead": "Des solutions web pensées pour des besoins concrets : sites vitrine, plate-formes de réservation, outils internes et applications métier.",
      "projects.filter.all": "Tous",
      "projects.filter.business": "Sites vitrine",
      "projects.filter.booking": "Réservation",
      "projects.filter.menu": "Menu digital",
      "projects.filter.django": "Application Django",
      "projects.empty.title": "Aucun projet publié pour le moment",
      "projects.empty.text": "Les réalisations apparaîtront ici dès qu'un projet sera ajouté depuis le back-office.",

      "contact.eyebrow": "Contact",
      "contact.title": "Travaillons ensemble",
      "contact.lead": "Pour une mission freelance, un stage ou simplement discuter d'un projet, je suis ouvert à la discussion.",
      "contact.info.location": "Localisation",
      "contact.info.email": "Email",
      "contact.info.phone": "Téléphone",
      "contact.form.name": "Nom complet",
      "contact.form.email": "Adresse email",
      "contact.form.message": "Message",
      "contact.form.submit": "Envoyer le message",
      "contact.form.note": "Le formulaire est prêt pour un traitement côté serveur. Pour un contact rapide, vous pouvez aussi m'écrire directement par email.",

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
      "hero.title": "Web developer &amp; information systems specialist focused on practical solutions",
      "hero.lead": "I design business websites, web applications and management tools that help organizations sell, organize and serve their customers more effectively.",
      "hero.cta.projects": "View projects",
      "hero.cta.contact": "Get in touch",

      "marquee.eyebrow": "Tools &amp; technologies",

      "home.featured.eyebrow": "Work",
      "home.featured.title": "What I build",
      "home.featured.note": "Business websites, web tools, booking systems and application logic designed around real needs.",
      "home.skills.frontend.title": "Front-end web experience",
      "home.skills.frontend.text": "Showcase sites, clear interfaces and customer journeys built for conversion, clarity and accessibility.",
      "home.skills.backend.title": "Business logic",
      "home.skills.backend.text": "Web applications, management tools, booking flows and PHP/Python solutions shaped around a real business need.",
      "home.skills.database.title": "Data & organization",
      "home.skills.database.text": "Content modeling, data management and structured tools designed to support business operations and user interfaces.",
      "home.skills.growth.title": "Professional approach",
      "home.skills.growth.text": "I combine business understanding, autonomy and continuous learning to deliver useful, durable digital solutions.",

      "home.cta.title": "Need a website or web tool?",
      "home.cta.text": "Open to freelance work, internships and junior development opportunities.",
      "home.cta.btn": "Get in touch",

      "about.eyebrow": "About",
      "about.title": "My journey",
      "about.p1": "I completed a MIAGE Licence at Université Kofi Annan de Guinée from 2023 to 2026 — a program combining software development, management and information systems.",
      "about.p2": "Today, I design business websites, booking systems, management tools and web solutions for local organizations. I am motivated by building products that solve real operational problems.",
      "about.p3": "Self-taught and practical, I also use AI-assisted development tools as a complement to a solid understanding of code and project logic.",
      "about.card1.title": "Approach",
      "about.card1.text": "Understanding the real need before writing code — every project starts from a concrete problem to solve with a clear, useful solution.",
      "about.card2.title": "Right now",
      "about.card2.text": "Transitioning from student to professional, I am looking for internships, freelance missions and junior development opportunities.",
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
      "experience.item2.title": "First concrete projects",
      "experience.item2.text": "I built real web and business solutions: Brasserie Artemis, SamiSpa, digital menus and the Gestion_Immo platform.",
      "experience.item3.date": "2026",
      "experience.item3.title": "Graduated &amp; first freelance work",
      "experience.item3.text": "Completed the MIAGE Licence 3, structured my activity and started taking on web development missions.",
      "experience.item4.date": "Now",
      "experience.item4.title": "Moving into the profession",
      "experience.item4.text": "I am actively looking for an internship, a junior role or freelance web projects.",

      "projects.eyebrow": "Projects",
      "projects.title": "Selected work",
      "projects.lead": "Web solutions built around real needs: business websites, booking systems, internal tools and practical application development.",
      "projects.filter.all": "All",
      "projects.filter.business": "Business sites",
      "projects.filter.booking": "Booking",
      "projects.filter.menu": "Digital menu",
      "projects.filter.django": "Django app",
      "projects.empty.title": "No public project yet",
      "projects.empty.text": "Published work will appear here as soon as it is added from the admin panel.",

      "contact.eyebrow": "Contact",
      "contact.title": "Let's work together",
      "contact.lead": "For a freelance mission, an internship or simply to discuss a project, I am open to the conversation.",
      "contact.info.location": "Location",
      "contact.info.email": "Email",
      "contact.info.phone": "Phone",
      "contact.form.name": "Full name",
      "contact.form.email": "Email address",
      "contact.form.message": "Message",
      "contact.form.submit": "Send message",
      "contact.form.note": "The form is ready for server-side handling. For a quick contact, you can also write directly by email.",

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
