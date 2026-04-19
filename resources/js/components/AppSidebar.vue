<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { isSidebarCollapsed } from '../composables/sidebarState';
const page = usePage()
const isActive = (componentName) => page.component === componentName

</script>

<template>
    <aside class="sidebar" :class="{ collapsed: isSidebarCollapsed }">
        <nav class="nav flex-column py-3">
            <!-- Main Menu Items -->
            <Link :href="route('dashboard')" class="nav-link" :class="{ active: isActive('Dashboard/Index') }">
                <i class="fas fa-tachometer-alt"></i>
                <span class="nav-text">Dashboard</span>
            </Link>

            <Link :href="route('properties.index')" class="nav-link" :class="{ active: isActive('Users/Index') }">

                <i class="fas fa-house"></i>
                <span class="nav-text">Properties</span>
            </Link>

            <Link :href="route('users.index')" class="nav-link" :class="{ active: isActive('Users/Index') }">
                <i class="fas fa-users"></i>
                <span class="nav-text">Users</span>
            </Link>
        </nav>
    </aside>
</template>

<style scoped>
.sidebar {
    width: 250px;
    transition: width 0.3s ease;
}

.sidebar.collapsed {
    width: 70px;
}

.nav-link {
    position: relative;
    display: flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    color: var(--bs-gray-700);
    transition: all 0.3s ease;
}

.nav-link:hover {
    color: var(--bs-primary);
    background-color: rgba(var(--bs-primary-rgb), 0.1);
}

.nav-link i {
    width: 24px;
    text-align: center;
}

.nav-text {
    margin-left: 12px;
    transition: opacity 0.3s ease;
}

.sidebar.collapsed .nav-text {
    opacity: 0;
    width: 0;
    height: 0;
    overflow: hidden;
}

/* Dropdown styles */
.dropdown-menu {
    position: relative;
    float: none;
    border: none;
    box-shadow: none;
    background-color: transparent;
    margin: 0;
    padding: 0;
}

.dropdown-item {
    padding: 0.5rem 1.5rem 0.5rem 3rem;
    color: var(--bs-gray-700);
}

.dropdown-item:hover,
.dropdown-item.active {
    color: var(--bs-primary);
    background-color: rgba(var(--bs-primary-rgb), 0.1);
}

/* Show submenu when collapsed and hovering */
.sidebar.collapsed .nav-item.dropdown:hover .dropdown-menu {
    display: block;
    position: absolute;
    left: 100%;
    top: 0;
    min-width: 200px;
    background: white;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    z-index: 1000;
}

/* Arrow for collapsed dropdown */
.sidebar.collapsed .nav-item.dropdown:hover .dropdown-menu::before {
    content: '';
    position: absolute;
    left: -6px;
    top: 15px;
    width: 0;
    height: 0;
    border-top: 6px solid transparent;
    border-bottom: 6px solid transparent;
    border-right: 6px solid white;
}
</style>