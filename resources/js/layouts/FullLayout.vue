<template>
  <v-app id="inspire">
    <v-app-bar>
      <v-app-bar-title
        @click="$router.push({ name: 'dashboard' })"
        class="cursor-pointer"
      >
        <v-icon class="mr-2">mdi-database</v-icon>
        Multi-Tenant Sync
      </v-app-bar-title>
    </v-app-bar>

    <v-main class="bg-grey-lighten-2">
      <v-container>
        <RouterView />
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup lang="ts">
import { RouterView } from "vue-router";
</script>
