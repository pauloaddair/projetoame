<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AME DJs | Inclusão em Alto Volume - Coletivo Profissional com Síndrome de Down</title>
    <meta name="description" content="Contrate DJs formados pelo Projeto AME para seu evento corporativo, feira, congresso ou festa. Inclusão produtiva real e experiência musical de alto nível.">
    
    <!-- Favicon & Mobile Touch Icons -->
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#0a0b10">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#0a0b10">

    <!-- Open Graph / Facebook / WhatsApp / LinkedIn -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://amedjs.ong.br/">
    <meta property="og:title" content="AME DJs | Inclusão em Alto Volume - Coletivo Profissional com Síndrome de Down">
    <meta property="og:description" content="Contrate DJs formados pelo Projeto AME para seu evento corporativo, feira, congresso ou festa. Inclusão produtiva real e experiência musical de alto nível.">
    <meta property="og:image" content="https://amedjs.ong.br/poster_AMEDJs.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Coletivo Oficial de DJs - Projeto AME">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="AME DJs">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://amedjs.ong.br/">
    <meta name="twitter:title" content="AME DJs | Inclusão em Alto Volume - Coletivo Profissional com Síndrome de Down">
    <meta name="twitter:description" content="Contrate DJs formados pelo Projeto AME para seu evento corporativo, feira, congresso ou festa. Inclusão produtiva real e experiência musical de alto nível.">
    <meta name="twitter:image" content="https://amedjs.ong.br/poster_AMEDJs.jpg">

    <!-- Bootstrap 5 CSS & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #00f2fe;
            --secondary: #4facfe;
            --accent: #ff007f;
            --bg-dark: #0a0b10;
            --card-bg: rgba(22, 27, 46, 0.85);
            --border-glow: rgba(79, 172, 254, 0.3);
        }
        
        body {
            background-color: var(--bg-dark);
            color: #f1f5f9;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        h1, h2, h3, .brand-font {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
        }

        /* Ambient Glows */
        .ambient-glow {
            position: absolute;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(255, 0, 127, 0.25) 0%, rgba(0, 242, 254, 0.15) 50%, transparent 70%);
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
            pointer-events: none;
        }

        .navbar {
            backdrop-filter: blur(15px);
            background: rgba(10, 11, 16, 0.85);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .hero-section {
            position: relative;
            padding: 120px 0 80px 0;
            z-index: 1;
        }

        .badge-pill-custom {
            background: rgba(0, 242, 254, 0.12);
            color: var(--primary);
            border: 1px solid rgba(0, 242, 254, 0.35);
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 0.88rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .text-gradient {
            background: linear-gradient(135deg, #00f2fe 0%, #4facfe 50%, #ff007f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #00f2fe 0%, #4facfe 100%);
            color: #050b14;
            font-weight: 700;
            border: none;
            padding: 14px 28px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 242, 254, 0.35);
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(0, 242, 254, 0.5);
            color: #000;
        }

        .btn-outline-glow {
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 14px 28px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.03);
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-outline-glow:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: rgba(255, 0, 127, 0.05);
        }

        .poster-card {
            background: var(--card-bg);
            border: 1px solid var(--border-glow);
            border-radius: 20px;
            padding: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
            transition: transform 0.4s ease;
        }

        .poster-card:hover {
            transform: scale(1.02);
            border-color: var(--primary);
        }

        .poster-card img {
            border-radius: 14px;
            width: 100%;
            height: auto;
            object-fit: cover;
        }

        .feature-card {
            background: var(--card-bg);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 18px;
            padding: 30px;
            height: 100%;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            border-color: var(--secondary);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(79, 172, 254, 0.15);
        }

        .icon-box {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: rgba(0, 242, 254, 0.1);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 20px;
        }

        .stat-card {
            border-left: 3px solid var(--accent);
            padding-left: 15px;
        }

        .contact-card {
            background: linear-gradient(145deg, rgba(22, 27, 46, 0.95), rgba(13, 16, 28, 0.95));
            border: 1px solid var(--border-glow);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
        }

        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #fff;
            padding: 12px 16px;
            border-radius: 10px;
        }

        .form-select option {
            background-color: #121624;
            color: #ffffff;
        }

        .form-control:focus, .form-select:focus {
            background-color: rgba(255, 255, 255, 0.08);
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(0, 242, 254, 0.2);
            color: #fff;
        }

        footer {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding: 50px 0 30px 0;
            background: #06070a;
        }

        /* Lightbox Fullscreen Modal */
        .lightbox-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(6, 7, 10, 0.94);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .lightbox-modal.active {
            opacity: 1;
            pointer-events: auto;
        }

        .lightbox-content {
            position: relative;
            max-width: 90vw;
            max-height: 88vh;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: scale(0.75);
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .lightbox-modal.active .lightbox-content {
            transform: scale(1);
        }

        .lightbox-img {
            max-width: 90vw;
            max-height: 85vh;
            object-fit: contain;
            border-radius: 16px;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.9), 0 0 30px rgba(0, 242, 254, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.15);
            user-select: none;
        }

        .lightbox-close {
            position: absolute;
            top: 25px;
            right: 25px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.3rem;
            transition: all 0.2s ease;
            z-index: 10001;
        }

        .lightbox-close:hover {
            background: rgba(255, 0, 127, 0.8);
            border-color: #ff007f;
            transform: scale(1.1);
        }

        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.2s ease;
            z-index: 10001;
        }

        .lightbox-nav:hover {
            background: rgba(0, 242, 254, 0.8);
            border-color: #00f2fe;
            color: #000;
            transform: translateY(-50%) scale(1.1);
        }

        .lightbox-prev { left: 30px; }
        .lightbox-next { right: 30px; }

        @media (max-width: 768px) {
            .lightbox-prev { left: 10px; width: 42px; height: 42px; }
            .lightbox-next { right: 10px; width: 42px; height: 42px; }
            .lightbox-close { top: 15px; right: 15px; width: 42px; height: 42px; }
        }
    </style>
</head>
<body>

    <!-- Ambient Lights -->
    <div class="ambient-glow" style="top: 50px; left: -100px;"></div>
    <div class="ambient-glow" style="top: 500px; right: -150px; background: radial-gradient(circle, rgba(79, 172, 254, 0.2) 0%, transparent 70%);"></div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <span class="fs-3 fw-bold text-gradient me-2"><i class="fas fa-compact-disc fa-spin" style="--fa-animation-duration: 6s;"></i> AME DJs</span>
                <span class="badge bg-secondary ms-2 text-uppercase" style="font-size: 0.65rem;">Coletivo Inclusivo</span>
            </a>
            <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item"><a class="nav-link text-white-50 px-3" href="#sobre">Sobre o Projeto</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50 px-3" href="#galeria">Galeria de Fotos</a></li>
                    <li class="nav-item"><a class="nav-link text-white-50 px-3" href="#contratacao">Contratação B2B</a></li>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-gradient btn-sm" href="#orcamento"><i class="fas fa-calendar-check me-2"></i>Contratar DJs</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <div class="badge-pill-custom mb-3">
                        <i class="fas fa-bolt me-2"></i>Iniciativa Oficial da Associação Projeto A.M.E.
                    </div>
                    <h1 class="display-3 fw-bold mb-4">
                        Inclusão no Ritmo Certo: <span class="text-gradient">Música que Transforma Eventos.</span>
                    </h1>
                    <p class="lead text-white-50 mb-5">
                        O primeiro coletivo profissional de DJs com Síndrome de Down capacitados para atuar em convenções, feiras corporativas, congressos e celebrações em todo o Brasil.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#orcamento" class="btn btn-gradient btn-lg">
                            <i class="fas fa-headphones me-2"></i>Solicitar Orçamento de DJ
                        </a>
                        <a href="https://www.projetoame.org/atendentes" target="_blank" class="btn btn-outline-glow btn-lg">
                            <i class="fas fa-graduation-cap me-2"></i>Conhecer a Formação AME
                        </a>
                    </div>

                    <div class="row mt-5 pt-4 g-4">
                        <div class="col-4">
                            <div class="stat-card">
                                <h3 class="fw-bold mb-0 text-white">3 Turmas</h3>
                                <small class="text-white-50">Instrutor DJ Pedro Vieira</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-card" style="border-color: var(--primary);">
                                <h3 class="fw-bold mb-0 text-white">+50</h3>
                                <small class="text-white-50">Horas de Prática</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-card" style="border-color: var(--secondary);">
                                <h3 class="fw-bold mb-0 text-white">100%</h3>
                                <small class="text-white-50">Inclusão Real (ESG)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="poster-card text-center">
                        <img src="poster_AMEDJs.jpg" alt="Cartaz Oficial AME DJs" class="img-fluid" onerror="this.src='https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&w=800&q=80'">
                        <div class="p-3 text-start">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-dark border border-secondary text-info">Aulas ministradas pelo DJ Pedro Vieira</span>
                                <small class="text-white-50"><i class="fas fa-map-marker-alt text-warning me-1"></i>Espaço SENAI Theobaldo de Nigris</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Diferenciais / Por que Contratar -->
    <section id="sobre" class="py-5" style="position: relative; z-index: 1;">
        <div class="container py-4">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="text-gradient fw-bold text-uppercase">Impacto Positivo & Experiência</span>
                <h2 class="display-5 fw-bold mt-2">Por que levar a AME DJs ao seu Evento?</h2>
                <p class="text-white-50">A união perfeita entre excelência técnica na pista e práticas concretas de responsabilidade social (ESG).</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Repertório Personalizado</h4>
                        <p class="text-white-50 mb-0">Sets montados sob medida para o perfil da sua empresa: lounge de networking, eletrônica comercial, pop, anos 80/90 ou celebração corporativa.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box" style="background: rgba(255, 0, 127, 0.1); color: var(--accent);">
                            <i class="fas fa-hand-holding-heart"></i>
                        </div>
                        <h4 class="fw-bold mb-3">ESG & Diversidade na Prática</h4>
                        <p class="text-white-50 mb-0">Contratação que materializa o compromisso corporativo com diversidade e inclusão, gerando remuneração justa e autonomia aos jovens artistas.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box" style="background: rgba(79, 172, 254, 0.1); color: var(--secondary);">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Supervisão Especializada</h4>
                        <p class="text-white-50 mb-0">Todos os DJs atuam acompanhados pela equipe técnica e monitores multidisciplinares do Projeto A.M.E., assegurando pontualidade e tranquilidade.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Galeria de Fotos Oficial (Grid 4x4 Dinâmico - 16 fotos com rotação) -->
    <section id="galeria" class="py-5" style="background: rgba(13, 16, 28, 0.6); border-top: 1px solid rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.05);">
        <div class="container py-4">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="text-gradient fw-bold text-uppercase"><i class="fas fa-camera-retro me-2"></i>Momentos & Performances</span>
                <h2 class="display-5 fw-bold mt-2">Nossos DJs em Ação</h2>
                <p class="text-white-50">Registros reais das aulas de capacitação técnica, formaturas no SENAI, eventos corporativos e vivências práticas do coletivo.</p>
            </div>

            <?php
            // Carregar todo o acervo de fotos disponíveis
            $todasFotos = glob('img/galeria/*.jpg');
            if (empty($todasFotos)) {
                $todasFotos = array_map(function($i) { return "img/galeria/dj_foto_{$i}.jpg"; }, range(1, 24));
            }
            // Embaralhar para o primeiro carregamento PHP
            $poolFotos = $todasFotos;
            shuffle($poolFotos);
            $fotosExibidas = array_slice($poolFotos, 0, 16);
            ?>

            <!-- Grid 4x4 Responsivo -->
            <div class="row g-3" id="galeria-grid" style="transition: opacity 0.5s ease;">
                <?php foreach ($fotosExibidas as $idx => $foto): ?>
                <div class="col-6 col-md-4 col-lg-3 galeria-col" data-index="<?php echo $idx; ?>">
                    <div class="card border-0 bg-transparent h-100 overflow-hidden shadow-sm" style="border-radius: 14px;">
                        <div class="d-block overflow-hidden position-relative group-gallery cursor-pointer" onclick="abrirLightbox('<?php echo htmlspecialchars($foto); ?>')" style="border-radius: 14px; border: 1px solid rgba(255,255,255,0.1); cursor: pointer;">
                            <img src="<?php echo htmlspecialchars($foto); ?>" alt="Momento DJ AME" class="img-fluid w-100 galeria-img" style="height: 220px; object-fit: cover; transition: transform 0.4s ease, opacity 0.5s ease;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'" loading="lazy">
                            <div class="position-absolute bottom-0 start-0 w-100 p-2 text-center" style="background: linear-gradient(to top, rgba(0,0,0,0.85), transparent); font-size: 0.75rem; color: #00f2fe; pointer-events: none;">
                                <i class="fas fa-search-plus me-1"></i>Ver ampliada
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4 px-2">
                <small class="text-white-50">
                    <i class="fas fa-images me-1"></i>Exibindo 16 momentos aleatórios de um acervo com mais de <strong><?php echo count($todasFotos); ?> fotos</strong>.
                </small>
                <button type="button" class="btn btn-outline-glow btn-sm" id="btn-reembaralhar" onclick="trocarFotosGaleria()">
                    <i class="fas fa-sync-alt me-1" id="icon-reload"></i>Ver mais...
                </button>
            </div>
        </div>
    </section>

    <!-- Formulário B2B de Orçamento -->
    <section id="orcamento" class="py-5">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="contact-card">
                        <div class="row g-5">
                            <div class="col-lg-5">
                                <span class="badge-pill-custom mb-3"><i class="fas fa-paper-plane me-2"></i>Contratação Rápida</span>
                                <h3 class="display-6 fw-bold mb-3">Leve essa energia para sua pista</h3>
                                <p class="text-white-50 mb-4">
                                    Preencha os dados do seu evento para receber nossa disponibilidade de datas, rider técnico e proposta de cachê solidário.
                                </p>
                                
                                <div class="mb-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-check-circle text-primary me-3 fs-5"></i>
                                        <span>Emissão de Nota Fiscal / Recibo Institucional (CNPJ)</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-check-circle text-primary me-3 fs-5"></i>
                                        <span>Equipamentos Pioneer DDJ homologados</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-primary me-3 fs-5"></i>
                                        <span>Atendimento em toda a Grande São Paulo e Região</span>
                                    </div>
                                </div>

                                <div class="p-3 rounded-3 bg-black border border-secondary mt-4">
                                    <small class="text-white-50 d-block mb-1">Dúvidas diretas com a coordenação:</small>
                                    <a href="https://wa.me/5511961631200?text=Ol%C3%A1%2C%20gostaria%20de%20informa%C3%A7%C3%B5es%20sobre%20o%20coletivo%20AME%20DJs!" target="_blank" class="text-info text-decoration-none fw-bold">
                                        <i class="fab fa-whatsapp me-2"></i>Fale com a nossa equipe
                                    </a>
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <form id="form-orcamento" onsubmit="enviarProposta(event)">
                                    <!-- Honeypot anti-spam -->
                                    <input type="text" name="website" style="display:none !important;" tabindex="-1" autocomplete="off">

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label text-white-50 small">Nome do Responsável / Solicitante *</label>
                                            <input type="text" name="nome" class="form-control" placeholder="Seu nome" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-white-50 small">Empresa / Agência Organizadora</label>
                                            <input type="text" name="empresa" class="form-control" placeholder="Nome da empresa">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-white-50 small">E-mail Corporativo *</label>
                                            <input type="email" name="email" class="form-control" placeholder="contato@empresa.com" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-white-50 small">WhatsApp / Telefone *</label>
                                            <input type="tel" name="telefone" class="form-control" placeholder="(11) 90000-0000" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-white-50 small">Tipo de Evento</label>
                                            <select name="tipo_evento" class="form-select">
                                                <option value="Corporativo / Congresso">Corporativo / Congresso / Feira</option>
                                                <option value="Festa de Fim de Ano">Festa de Confraternização</option>
                                                <option value="Casamento / Aniversário">Casamento / Aniversário</option>
                                                <option value="Outro">Outro tipo de celebração</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-white-50 small">Data Estimada do Evento</label>
                                            <input type="date" name="data_evento" class="form-control">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label text-white-50 small">Local e detalhes (cidade, horário aproximado, público)</label>
                                            <textarea name="mensagem" class="form-control" rows="3" placeholder="Conte mais sobre o formato do evento e a necessidade musical..."></textarea>
                                        </div>

                                        <!-- Alerta de feedback -->
                                        <div class="col-12" id="form-feedback" style="display: none;"></div>

                                        <div class="col-12 mt-3">
                                            <button type="submit" id="btn-submit-proposta" class="btn btn-gradient w-100 py-3">
                                                <i class="fas fa-paper-plane me-2"></i><span>Enviar Solicitação de Proposta</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-md-6 text-center text-md-start">
                    <h5 class="fw-bold mb-1 text-gradient">AME DJs — Coletivo Musical Inclusivo</h5>
                    <p class="small text-white-50 mb-0">
                        Iniciativa vinculada à Associação Projeto A.M.E. — CNPJ 32.131.752/0001-88.<br>
                        Transformando capacidades em protagonismo artístico e inclusão no trabalho.
                    </p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="https://www.projetoame.org" class="text-white-50 text-decoration-none me-3 small hover-white">Portal Projeto AME</a>
                    <a href="https://lovecoffee.ong.br" class="text-white-50 text-decoration-none me-3 small hover-white">LOVE Coffee</a>
                    <span class="small text-white-50">© 2026 AME DJs. Todos os direitos reservados.</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Lightbox Zoom Modal Fullscreen -->
    <div id="lightbox-modal" class="lightbox-modal" role="dialog" aria-modal="true" onclick="fecharLightbox(event)">
        <button type="button" class="lightbox-close" onclick="fecharLightbox(event)" aria-label="Fechar"><i class="fas fa-times"></i></button>
        <button type="button" class="lightbox-nav lightbox-prev" onclick="navegarLightbox(-1, event)" aria-label="Anterior"><i class="fas fa-chevron-left"></i></button>
        <button type="button" class="lightbox-nav lightbox-next" onclick="navegarLightbox(1, event)" aria-label="Próxima"><i class="fas fa-chevron-right"></i></button>
        <div class="lightbox-content" onclick="event.stopPropagation()">
            <img id="lightbox-img" class="lightbox-img" src="" alt="Momento DJ AME Ampliado">
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script de Rotação Automática da Galeria e Lightbox Zoom -->
    <script>
    (function() {
        const fotosAcervo = <?php echo json_encode(array_values($todasFotos)); ?>;
        const grid = document.getElementById('galeria-grid');
        const iconReload = document.getElementById('icon-reload');
        const modal = document.getElementById('lightbox-modal');
        const modalImg = document.getElementById('lightbox-img');
        let indexAtualLightbox = -1;
        let intervalo = null;

        // --- Lightbox Zoom Fullscreen ---
        window.abrirLightbox = function(src) {
            indexAtualLightbox = fotosAcervo.indexOf(src);
            if (indexAtualLightbox === -1) indexAtualLightbox = 0;
            modalImg.src = src;
            modal.classList.add('active');
            document.body.style.overflow = 'hidden'; // trava scroll de fundo
        };

        window.fecharLightbox = function(e) {
            if (e) e.stopPropagation();
            modal.classList.remove('active');
            document.body.style.overflow = '';
        };

        window.navegarLightbox = function(direcao, e) {
            if (e) e.stopPropagation();
            if (fotosAcervo.length === 0) return;
            indexAtualLightbox = (indexAtualLightbox + direcao + fotosAcervo.length) % fotosAcervo.length;
            
            // transição suave ao trocar de foto
            modalImg.style.opacity = '0.3';
            modalImg.style.transform = 'scale(0.95)';
            setTimeout(() => {
                modalImg.src = fotosAcervo[indexAtualLightbox];
                modalImg.style.opacity = '1';
                modalImg.style.transform = 'scale(1)';
            }, 150);
        };

        // Teclado: ESC fecha, Setas navegam
        document.addEventListener('keydown', function(e) {
            if (!modal.classList.contains('active')) return;
            if (e.key === 'Escape') window.fecharLightbox();
            if (e.key === 'ArrowLeft') window.navegarLightbox(-1);
            if (e.key === 'ArrowRight') window.navegarLightbox(1);
        });

        // --- Rotação da Galeria (Grid 4x4) ---
        function embaralhar(arr) {
            const copia = [...arr];
            for (let i = copia.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [copia[i], copia[j]] = [copia[j], copia[i]];
            }
            return copia;
        }

        window.trocarFotosGaleria = function() {
            if (!grid || fotosAcervo.length <= 16) return;
            if (iconReload) iconReload.classList.add('fa-spin');

            const novas = embaralhar(fotosAcervo).slice(0, 16);
            const cards = grid.querySelectorAll('.galeria-col');

            grid.style.opacity = '0.2';

            setTimeout(() => {
                cards.forEach((card, idx) => {
                    if (novas[idx]) {
                        const trigger = card.querySelector('.group-gallery');
                        const img = card.querySelector('img');
                        if (trigger && img) {
                            trigger.setAttribute('onclick', `abrirLightbox('${novas[idx]}')`);
                            img.src = novas[idx];
                        }
                    }
                });

                grid.style.opacity = '1';
                if (iconReload) iconReload.classList.remove('fa-spin');
            }, 350);
        };

        // Rotação a cada 30s
        intervalo = setInterval(window.trocarFotosGaleria, 30000);

        // --- Envio de Formulário B2B (AJAX -> /api/proposta.php) ---
        window.enviarProposta = async function(e) {
            e.preventDefault();
            const form = document.getElementById('form-orcamento');
            const btn = document.getElementById('btn-submit-proposta');
            const feedback = document.getElementById('form-feedback');
            const btnOriginal = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando proposta...';
            feedback.style.display = 'none';

            try {
                const formData = new FormData(form);
                const resp = await fetch('enviar_proposta.php', {
                    method: 'POST',
                    body: formData
                });
                const res = await resp.json();

                if (res.success) {
                    feedback.className = 'col-12 p-3 rounded-3 text-center mb-2';
                    feedback.style.background = 'rgba(0, 242, 254, 0.15)';
                    feedback.style.border = '1px solid #00f2fe';
                    feedback.style.color = '#00f2fe';
                    feedback.innerHTML = '<i class="fas fa-check-circle me-2 fs-5"></i><strong>Sucesso!</strong> ' + (res.message || 'Recebemos seu contato com sucesso!');
                    feedback.style.display = 'block';
                    form.reset();
                } else {
                    throw new Error(res.error || 'Ocorreu um erro ao processar. Tente novamente.');
                }
            } catch (err) {
                feedback.className = 'col-12 p-3 rounded-3 text-center mb-2';
                feedback.style.background = 'rgba(255, 0, 127, 0.15)';
                feedback.style.border = '1px solid #ff007f';
                feedback.style.color = '#ff66b2';
                feedback.innerHTML = '<i class="fas fa-exclamation-triangle me-2 fs-5"></i>' + err.message;
                feedback.style.display = 'block';
            } finally {
                btn.disabled = false;
                btn.innerHTML = btnOriginal;
            }
        };
    })();
    </script>
</body>
</html>
