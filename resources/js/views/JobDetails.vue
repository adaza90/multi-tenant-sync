<template>
  <v-row>
    <v-col cols="12">
      <UiParentCard :title="jobExecution.name">
        <v-list density="compact">
          <v-list-item
            color="primary"
            subtitle="Batch ID"
            :title="jobExecution.batch_id"
          />
          <v-list-item
            color="primary"
            subtitle="Name"
            :title="jobExecution.name"
          />
          <v-list-item
            color="primary"
            subtitle="Job"
            :title="jobExecution.job"
          />
          <v-list-item
            color="primary"
            subtitle="Created At"
            :title="jobExecution.created_at"
          />
        </v-list>

        <div v-if="jobExecution.params" class="py-5">
          <HighCode :codeValue="jobExecution.params" width="100%" />
        </div>

        <v-row class="mt-5">
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
          <template v-slot:item.success="{ item }">
            <v-chip v-if="item.success" class="ma-2" color="cyan" label
              >Success</v-chip
            >
            <v-chip v-else class="ma-2" color="pink" label>Error</v-chip>
          </template>
          <template v-slot:item.response="{ item }">
            <v-dialog max-width="500">
              <template v-slot:activator="{ props: activatorProps }">
                <v-btn
                  v-bind="activatorProps"
                  color="surface-variant"
                  text="View Response"
                  variant="flat"
                ></v-btn>
              </template>

              <template v-slot:default="{ isActive }">
                <v-card title="Response">
                  <div class="px-5 py-5">
                    <HighCode :codeValue="item.response" width="100%" />
                  </div>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text="Close" @click="isActive.value = false"></v-btn>
                  </v-card-actions>
                </v-card>
              </template>
            </v-dialog>
          </template>
        </v-data-table-server>
      </UiParentCard>
    </v-col>
  </v-row>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { useRoute } from "vue-router";
import { useJobExecutionStore } from "@/stores/jobExecution";
import UiParentCard from "@/components/UiParentCard.vue";
import { HighCode } from "vue-highlight-code";

//
const route = useRoute();
const storeJobs = useJobExecutionStore();
const jobExecutionId = ref(null);
const jobExecution = computed(() => storeJobs.jobExecution);

watch(
  () => route.params.jobExecutionId,
  (newJobExecutionId, oldJobExecutionId) => {
    jobExecutionId.value = parseInt(newJobExecutionId);
    if (!isNaN(jobExecutionId.value)) {
      storeJobs.getJobExecutionById(jobExecutionId.value);
    }
  },
  { immediate: true }
);

//
const headers = ref([
  { title: "Tenant", align: "start", key: "tenant_name" },
  { title: "Status", align: "start", key: "success" },
  { title: "Response", align: "start", key: "response" },
  { title: "Date", align: "start", key: "created_at" },
]);
const search = ref("");
const sortBy = ref([]);
const sortColumn = ref("created_at");
const sortDirection = ref("desc");
const items = computed(() => storeJobs.jobExecutionsLogs);
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
  storeJobs.getJobExecutionsLogs({
    jobExecutionId: jobExecutionId.value,
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
