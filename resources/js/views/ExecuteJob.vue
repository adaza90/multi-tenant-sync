<template>
  <v-row>
    <v-col cols="6">
      <UiParentCard title="Execute Job">
        <v-select
          v-if="!$route.params.projectId"
          v-model="projectId"
          label="Project"
          :items="projects"
          variant="solo"
          item-value="id"
          item-title="name"
        >
          <template v-slot:item="{ props, item }">
            <v-list-item
              v-bind="props"
              :subtitle="item.raw.db_host"
            ></v-list-item> </template
        ></v-select>

        <v-select
          v-model="jobId"
          label="Job"
          :items="jobsAvailable"
          variant="solo"
        >
          <template v-slot:item="{ props, item }">
            <v-list-item
              v-bind="props"
              :subtitle="item.raw.file"
            ></v-list-item> </template
        ></v-select>

        <v-text-field
          variant="solo"
          hide-details
          v-model="name"
          label="Details (Optional)"
          class="mb-5"
        ></v-text-field>

        <v-textarea
          v-if="selectedJob.allow_params"
          v-model="params"
          label="Params"
          variant="solo"
          clearable
        ></v-textarea>

        <v-btn
          color="primary"
          flat
          class="mt-5"
          @click="executeJob()"
          :disabled="loading"
        >
          <v-icon class="mr-2">mdi-database-sync</v-icon>
          Execute Job
        </v-btn>
      </UiParentCard>
    </v-col>
  </v-row>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useProjectStore } from "@/stores/project";
import { useJobExecutionStore } from "@/stores/jobExecution";
import UiParentCard from "@/components/UiParentCard.vue";

//
const route = useRoute();
const router = useRouter();
const storeProjects = useProjectStore();
const storeJobs = useJobExecutionStore();

storeJobs.getJobsAvailable();
storeProjects.getProjects();

//
const jobId = ref(null);
const projectId = ref(null);
const name = ref(null);
const params = ref(null);
const loading = computed(() => storeJobs.loading);
const jobsAvailable = computed(() => storeJobs.jobsAvailable);
const projects = computed(() => storeProjects.projects);
const selectedJob = computed(
  () => jobsAvailable.value.find((job) => job.value === jobId.value) ?? {}
);

const executeJob = () => {
  storeJobs
    .executeJob(projectId.value, jobId.value, name.value, params.value)
    .then((response) => {
      router.push({
        name: "job-details",
        params: {
          jobExecutionId: response.jobExecution.id,
        },
      });
    });
};

watch(
  () => route.params.projectId,
  (newProjectId, oldProjectId) => {
    projectId.value = parseInt(newProjectId);
    if (isNaN(projectId.value)) {
      projectId.value = null;
    }
  },
  { immediate: true }
);
</script>
