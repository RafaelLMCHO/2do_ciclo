<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colegio Los Angeles - Sistema de Gestion Escolar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 40%, #1e40af 100%);
            color: #fff;
            overflow-x: hidden;
        }

        .bg-pattern {
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(96, 165, 250, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 50% 80%, rgba(37, 99, 235, 0.12) 0%, transparent 50%);
            pointer-events: none;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        /* Navbar */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 0;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .logo-text {
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: -0.02em;
        }

        .logo-text span {
            color: #93c5fd;
            font-weight: 400;
        }

        .nav-btn {
            padding: 0.7rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s;
        }

        .nav-btn-outline {
            border: 1.5px solid rgba(255,255,255,0.25);
            color: #fff;
        }

        .nav-btn-outline:hover {
            border-color: #60a5fa;
            background: rgba(96, 165, 250, 0.1);
        }

        /* Hero */
        .hero {
            text-align: center;
            padding: 5rem 0 3rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 50px;
            padding: 0.4rem 1rem;
            font-size: 0.8rem;
            font-weight: 500;
            color: #93c5fd;
            margin-bottom: 2rem;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            background: #60a5fa;
            border-radius: 50%;
        }

        h1 {
            font-size: clamp(2.5rem, 6vw, 4rem);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.03em;
            margin-bottom: 1.5rem;
        }

        h1 .highlight {
            background: linear-gradient(135deg, #60a5fa, #93c5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            font-size: 1.15rem;
            color: #94a3b8;
            max-width: 600px;
            margin: 0 auto 2.5rem;
            line-height: 1.7;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.9rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: #fff;
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(37, 99, 235, 0.5);
        }

        .btn-secondary {
            background: rgba(255,255,255,0.08);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.15);
        }

        .btn-secondary:hover {
            background: rgba(255,255,255,0.12);
            transform: translateY(-2px);
        }

        /* Features */
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            padding: 4rem 0;
        }

        .feature-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s;
        }

        .feature-card:hover {
            background: rgba(255,255,255,0.07);
            border-color: rgba(59, 130, 246, 0.3);
            transform: translateY(-4px);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 1.2rem;
        }

        .feature-icon.blue { background: rgba(59, 130, 246, 0.15); }
        .feature-icon.green { background: rgba(34, 197, 94, 0.15); }
        .feature-icon.purple { background: rgba(168, 85, 247, 0.15); }
        .feature-icon.orange { background: rgba(251, 146, 60, 0.15); }
        .feature-icon.pink { background: rgba(236, 72, 153, 0.15); }
        .feature-icon.teal { background: rgba(20, 184, 166, 0.15); }

        .feature-card h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.6rem;
        }

        .feature-card p {
            font-size: 0.9rem;
            color: #94a3b8;
            line-height: 1.6;
        }

        /* Roles section */
        .roles {
            text-align: center;
            padding: 3rem 0 5rem;
        }

        .roles h2 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.7rem;
        }

        .roles .roles-subtitle {
            color: #94a3b8;
            margin-bottom: 2.5rem;
            font-size: 1.05rem;
        }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            max-width: 750px;
            margin: 0 auto;
        }

        .role-item {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 1.5rem 1rem;
            transition: all 0.3s;
        }

        .role-item:hover {
            border-color: rgba(59, 130, 246, 0.3);
            background: rgba(59, 130, 246, 0.06);
        }

        .role-emoji {
            font-size: 2rem;
            margin-bottom: 0.7rem;
        }

        .role-item h4 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .role-item p {
            font-size: 0.8rem;
            color: #94a3b8;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 2rem 0;
            border-top: 1px solid rgba(255,255,255,0.06);
            color: #64748b;
            font-size: 0.85rem;
        }

        @media (max-width: 640px) {
            .hero { padding: 3rem 0 2rem; }
            nav { flex-direction: column; gap: 1rem; }
        }
    </style>
</head>
<body>
    <div class="bg-pattern"></div>

    <div class="container">
        <nav>
            <div class="logo">
                <div class="logo-icon">🎓</div>
                <div class="logo-text">Colegio Los Angeles <span>| Gestion Escolar</span></div>
            </div>
            <a href="{{ route('login') }}" class="nav-btn nav-btn-outline">Iniciar Sesion</a>
        </nav>

        <section class="hero">
            <div class="badge">
                <span class="badge-dot"></span>
                Plataforma de Gestion Educativa
            </div>

            <h1>
                Sistema de Gestion<br>
                <span class="highlight">Escolar Integral</span>
            </h1>

            <p class="subtitle">
                Administra alumnos, profesores, horarios, calificaciones y toda la gestion academica del Colegio "Los Angeles" en un solo lugar.
            </p>

            <div class="hero-buttons">
                <a href="{{ route('login') }}" class="btn btn-primary">
                    Ingresar al Sistema →
                </a>
                <a href="#modulos" class="btn btn-secondary">
                    Ver Modulos
                </a>
            </div>
        </section>

        <section class="features" id="modulos">
            <div class="feature-card">
                <div class="feature-icon blue">👨‍🎓</div>
                <h3>Gestion de Alumnos</h3>
                <p>Registro, edicion y administracion completa de datos estudiantiles.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon green">👩‍🏫</div>
                <h3>Gestion de Profesores</h3>
                <p>Administra el personal docente, permisos y asignaciones de materias.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon purple">📅</div>
                <h3>Horarios y Turnos</h3>
                <p>Organizacion de horarios por profesor, curso y paralelo.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon orange">📊</div>
                <h3>Calificaciones</h3>
                <p>Consulta de notas por trimestre con evaluacion integral.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon pink">📋</div>
                <h3>Bitacora de Accesos</h3>
                <p>Registro completo de actividad y accesos al sistema.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon teal">⚙️</div>
                <h3>Configuracion</h3>
                <p>Gestiones academicas, niveles y configuracion general del sistema.</p>
            </div>
        </section>

        <section class="roles">
            <h2>Acceso por Roles</h2>
            <p class="roles-subtitle">Cada usuario tiene acceso a las herramientas que necesita</p>

            <div class="roles-grid">
                <div class="role-item">
                    <div class="role-emoji">🔧</div>
                    <h4>Administrador</h4>
                    <p>Control total del sistema</p>
                </div>
                <div class="role-item">
                    <div class="role-emoji">👨‍🏫</div>
                    <h4>Profesor</h4>
                    <p>Horarios y asignaturas</p>
                </div>
                <div class="role-item">
                    <div class="role-emoji">👪</div>
                    <h4>Apoderado</h4>
                    <p>Consulta de notas</p>
                </div>
            </div>
        </section>

        <footer>
            <p>© {{ date('Y') }} Colegio "Los Angeles" — Sistema de Gestion Escolar</p>
        </footer>
    </div>
</body>
</html>
