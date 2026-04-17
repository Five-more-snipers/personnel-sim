<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
            <div class="container">
                <Link href="/" class="navbar-brand fw-bold">
                    <i class="bi bi-globe"></i> Personnel Sim</Link>
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <Link href="/" class="nav-link">Dashboard</Link>
                        </li>
                        <li class="nav-item">
                            <Link href="/personnel/create" class="nav-link">Deploy Personnel</Link>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Master Data
                            </a>
                            <ul class="dropdown-menu shadow-sm">
                                <li><Link href="/factions" class="dropdown-item">Factions</Link></li>
                                <li><Link href="/ranks" class="dropdown-item">Ranks</Link></li>
                                <li><Link href="/unit-classes" class="dropdown-item">Unit Classes</Link></li>
                                <li><Link href="/weapons" class="dropdown-item">Weapons</Link></li>
                            </ul>
                        </li>
                        </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <slot />
        </main>
    </div>
</template>