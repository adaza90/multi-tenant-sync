<template>
  <v-row>
    <v-col cols="12">
      <UiParentCard :title="project.name">
        <v-row>
          <v-col cols="12" lg="4" md="6">
            <v-text-field
              density="compact"
              v-model="search"
              label="Search Job"
              prepend-inner-icon="mdi-magnify"
              variant="outlined"
              hide-details
            ></v-text-field>
          </v-col>

          <v-col cols="12" lg="8" md="6" class="text-right">
            <v-btn
              color="primary"
              :to="{
                name: 'execute-job',
                params: { projectId: $route.params.projectId },
              }"
              flat
              class="ml-auto"
            >
              <v-icon class="mr-2">mdi-database-sync</v-icon>
              Execute Job
            </v-btn>
          </v-col>
        </v-row>

        <v-data-table-server
          :headers="headers"
          :items="items"
          :loading="loading"
          :items-length="totalItems"
          v-model:items-per-page="itemsPerPage"
          @update:options="loadData"
          :search="search"
          @search="updateSearch"
          v-model:sort-by="sortBy"
          item-value="name"
          class="border rounded-md mt-5"
        >
          <template v-slot:item.name="{ item }">
            <RouterLink
              :to="{
                name: 'job-details',
                params: {
                  jobExecutionId: item.id,
                },
              }"
              class="text-primary text-decoration-none text-body-1 opacity-1 font-weight-medium"
            >
              {{ item.name }}
            </RouterLink>
          </template>
          <template v-slot:item.progress="{ item }">
            <v-chip
              v-if="
                !item.cancelled_at &&
                item.failed_jobs > 0 &&
                item.total_jobs - item.pending_jobs < item.total_jobs
              "
              class="ma-2"
              color="pink"
              label
              >Failures</v-chip
            >

            <v-chip
              v-if="
                !item.cancelled_at &&
                item.total_jobs - item.pending_jobs == item.total_jobs
              "
              class="ma-2"
              color="cyan"
              label
              >Finished</v-chip
            >

            <v-chip
              v-if="
                !item.cancelled_at && item.pending_jobs > 0 && !item.failed_jobs
              "
              class="ma-2"
              color="orange"
              label
              >Pending</v-chip
            >

            <v-chip v-if="item.cancelled_at" class="ma-2" color="pink" label
              >Cancelled</v-chip
            >
          </template>
        </v-data-table-server>
      </UiParentCard>
    </v-col>
  </v-row>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { useRoute } from "vue-router";
import { useProjectStore } from "@/stores/project";
import { useJobExecutionStore } from "@/stores/jobExecution";
import UiParentCard from "@/components/UiParentCard.vue";

//
const route = useRoute();
const store = useProjectStore();
const storeJobs = useJobExecutionStore();
const project = computed(() => store.project);
const projectId = ref(null);

watch(
  () => route.params.projectId,
  (newProjectId, oldProjectId) => {
    projectId.value = parseInt(newProjectId);
    if (!isNaN(projectId.value)) {
      store.getProjectById(projectId.value);
    }
  },
  { immediate: true }
);

//
const headers = ref([
  { title: "Name", align: "start", key: "name" },
  { title: "Total", align: "start", key: "total_jobs" },
  { title: "Pending", align: "start", key: "pending_jobs" },
  { title: "Failed", align: "start", key: "failed_jobs" },
  { title: "Status", align: "start", key: "progress" },
  { title: "Date", align: "start", key: "created_at" },
]);
const search = ref("");
const sortBy = ref([]);
const sortColumn = ref("created_at");
const sortDirection = ref("desc");
const items = computed(() => storeJobs.jobExecutions);
const loading = computed(() => storeJobs.loading);
const totalItems = computed(() => storeJobs.totalItems);
const itemsPerPage = ref(100);
const currentPage = ref(0);

const loadData = ({
  page,
  itemsPerPage,
}: {
  page: number;
  itemsPerPage: number;
}) => {
  currentPage.value = page;
  storeJobs.getJobExecutions({
    projectId: projectId.value,
    page: page,
    itemsPerPage: itemsPerPage,
    search: search.value,
    sortColumn: sortColumn.value,
    sortDirection: sortDirection.value,
  });
};

const updateSearch = () => {
  loadData({
    page: 0,
    itemsPerPage: itemsPerPage.value,
  });
};

watch(itemsPerPage, (newValue, oldValue) => {
  if (newValue !== oldValue) {
    loadData({
      page: 0,
      itemsPerPage: newValue,
    });
  }
});

watch(sortBy, (newValue, oldValue) => {
  if (newValue[0]) {
    sortColumn.value = newValue[0].key;
    sortDirection.value = newValue[0].order;
  }
});
</script>
