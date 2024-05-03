<template>
  <v-row>
    <v-col class="mt-2" cols="12">
      <strong>Projects</strong>
    </v-col>

    <v-col v-for="project in projects" :key="project.id" cols="4" md="4">
      <v-card elevation="16" link>
        <v-card-item class="text-center">
          <v-card-title
            @click="
              $router.push({
                name: 'project',
                params: {
                  projectId: project.id,
                },
              })
            "
          >
            <v-avatar size="80" v-if="project.logo">
              <v-img :alt="project.name" :src="project.logo"></v-img>
            </v-avatar>
            <v-avatar size="80" :color="getRandomColor()" v-else>
              <span class="text-h3">{{ getInitials(project.name) }}</span>
            </v-avatar>
            <h4>{{ project.name }}</h4>
          </v-card-title>

          <v-card-subtitle>{{ project.db_host }}</v-card-subtitle>

          <v-card-actions>
            <v-btn icon flat @click="editItem(project)">
              <v-icon class="mr-2">mdi-pencil</v-icon>
            </v-btn>
          </v-card-actions>
        </v-card-item>
      </v-card>
    </v-col>

    <CreateProject
      :projectId="projectId"
      :dialogEdit="dialogEdit"
      @closeEditModal="closeEditModal"
      @refreshProjects="loadData()"
    />
  </v-row>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import CreateProject from "./CreateProject.vue";
import { useProjectStore } from "@/stores/project";
import { getRandomColor, getInitials } from "@/utils/common";

const store = useProjectStore();
const projects = computed(() => store.projects);
const dialogEdit = ref(false);
const projectId = ref(-1);

const loadData = () => {
  store.getProjects();
};

function editItem(project: Array<any>) {
  projectId.value = project.id;
  dialogEdit.value = true;
}

function closeEditModal() {
  projectId.value = -1;
  dialogEdit.value = false;
}

loadData();
</script>
