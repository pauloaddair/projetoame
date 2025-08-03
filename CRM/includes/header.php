<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Pipeline de Prospecção</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .kanban-board {
            min-height: calc(100vh - 150px);
        }
        .kanban-stage {
            min-height: 200px;
            padding: 10px;
        }
        .opportunity-card {
            cursor: move;
        }
        .opportunity-card.dragging {
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">CRM</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/CRM/kanban">Andamento</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/CRM/promoters">Promotores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/CRM/exhibitors">Expositores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/CRM/events">Eventos</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>