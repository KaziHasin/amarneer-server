<script setup>
import { toggleSidebar } from '../composables/sidebarState'

</script>

<template>
  <header class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
    <div class="container-fluid">
      <button class="btn btn-link me-2" @click="toggleSidebar">
        <i class="fas fa-bars"></i>
      </button>
      <a class="navbar-brand" href="#">AdminPanel</a>

      <div class="ms-auto d-flex align-items-center">
        <b-dropdown variant="link" toggle-class="text-decoration-none" no-caret>
          <template #button-content>
            <div class="d-flex align-items-center">
              <img src="https://avatar.iran.liara.run/username?username=Admin+User&background=b9c5f8&color=3f37c9"
                class="rounded-circle me-2 " width="40" height="40" :alt="'Admin User'">

              <span class="d-none d-md-inline">Admin User</span>
            </div>
          </template>

          <b-dropdown-item href="#">
            <i class="fas fa-user me-2"></i> Profile
          </b-dropdown-item>
          <b-dropdown-item href="#">
            <i class="fas fa-cog me-2"></i> Settings
          </b-dropdown-item>
          <b-dropdown-divider></b-dropdown-divider>
          <b-dropdown-item href="#">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
          </b-dropdown-item>
        </b-dropdown>

        <button class="btn btn-link ms-2">
          <i class="fas fa-bell"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            3
          </span>
        </button>
      </div>
    </div>
  </header>
</template>
