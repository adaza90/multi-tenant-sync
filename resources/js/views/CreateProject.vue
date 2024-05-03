<template>
  <v-dialog v-model="dialog" max-width="800">
    <template v-slot:activator="{ props }">
      <v-btn color="primary" v-bind="props" flat class="ml-auto">
        <v-icon class="mr-2">mdi-account-multiple-plus</v-icon>
        New Project
      </v-btn>
    </template>
    <v-card>
      <v-card-title class="pa-4 bg-secondary">
        <span class="title text-white">
          <v-icon class="mr-2">mdi-account-multiple-plus</v-icon>
          {{ formTitle }}
        </span>
      </v-card-title>

      <v-card-text>
        <v-form ref="form" v-model="valid" lazy-validation>
          <v-row>
            <v-col cols="12" sm="12">
              <v-text-field
                variant="outlined"
                hide-details
                v-model="editedItem.name"
                label="Name"
                @blur="v$.name.$touch"
                @input="v$.name.$touch"
                :error-messages="v$.name.$errors.map((e: any) => e.$message)"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="9">
              <v-text-field
                variant="outlined"
                hide-details
                v-model="editedItem.db_host"
                label="Host"
                required
                @blur="v$.db_host.$touch"
                @input="v$.db_host.$touch"
                :error-messages="v$.db_host.$errors.map((e: any) => e.$message)"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="3">
              <v-text-field
                variant="outlined"
                hide-details
                v-model="editedItem.db_port"
                label="Port"
                type="number"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="12">
              <v-text-field
                variant="outlined"
                hide-details
                v-model="editedItem.db_database"
                label="Database Name"
                @blur="v$.db_database.$touch"
                @input="v$.db_database.$touch"
                :error-messages="v$.db_database.$errors.map((e: any) => e.$message)"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="12">
              <v-text-field
                variant="outlined"
                hide-details
                v-model="editedItem.db_username"
                label="Username"
                autocomplete="new-username"
                @blur="v$.db_username.$touch"
                @input="v$.db_username.$touch"
                :error-messages="v$.db_username.$errors.map((e: any) => e.$message)"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="12">
              <v-text-field
                variant="outlined"
                hide-details
                type="password"
                v-model="editedItem.db_password"
                label="Password"
                autocomplete="new-password"
                @blur="v$.db_password.$touch"
                @input="v$.db_password.$touch"
                :error-messages="v$.db_password.$errors.map((e: any) => e.$message)"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="12">
              <v-textarea
                variant="outlined"
                hide-details
                v-model="editedItem.raw_query"
                label="Query"
                @blur="v$.raw_query.$touch"
                @input="v$.raw_query.$touch"
                :error-messages="v$.raw_query.$errors.map((e: any) => e.$message)"
              ></v-textarea>
            </v-col>
          </v-row>
        </v-form>
      </v-card-text>
      <v-card-actions class="pa-4">
        <v-spacer></v-spacer>
        <v-btn color="error" @click="close"> Cancel </v-btn>
        <v-btn
          color="secondary"
          :disabled="editedItem.name == ''"
          variant="flat"
          @click="save"
        >
          <span v-if="projectId === -1">Save</span>
          <span v-else>Edit</span>
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { email, required } from "@vuelidate/validators";
import { useProjectStore } from "@/stores/project";

const props = defineProps({
  projectId: Number,
  dialogEdit: Boolean,
});

const emits = defineEmits(["closeEditModal", "refreshProjects"]);

// Computed Property
const formTitle = computed(() => {
  return projectId.value === -1 ? "New Project" : "Edit Project";
});

const store = useProjectStore();
const valid = ref(true);
const dialog = ref(false);
const projectId = ref(-1);
const editedItem = ref({
  name: "",
  logo: "",
  db_host: "",
  db_port: 3306,
  db_database: "",
  db_username: "",
  db_password: "",
  raw_query: "",
});

const defaultItem = ref({
  name: "",
  logo: "",
  db_host: "",
  db_port: 3306,
  db_database: "",
  db_username: "",
  db_password: "",
  raw_query: "",
});

watch(
  [() => props.dialogEdit, () => props.projectId],
  ([newDialogEdit, newProjectId], [oldDialogEdit, oldProjectId]) => {
    dialog.value = newDialogEdit;
    projectId.value = newProjectId ?? 0;

    if (typeof newProjectId === "number" && newProjectId > 0 && dialog.value) {
      store.getProjectById(newProjectId).then((response) => {
        editedItem.value = Object.assign({}, response.data.project);
      });
    }
  }
);

const v$ = useVuelidate(
  {
    name: { required },
    db_host: { required },
    db_database: { required },
    db_username: { required },
    db_password: { required },
    raw_query: { required },
    db_host: { required },
  },
  editedItem
);

function close() {
  dialog.value = false;
  v$.value.$reset();
  emits("closeEditModal");
  setTimeout(() => {
    editedItem.value = Object.assign({}, defaultItem.value);
    projectId.value = -1;
  }, 300);
}

function save() {
  if (v$.value.$invalid) {
    alert("Validation error: All form fields must be completed.");
    return;
  }

  if (projectId.value > -1) {
    store
      .updateProject(projectId.value, editedItem.value)
      .then((response) => {
        close();
        alert("Project updated successfully!");
        emits("refreshProjects");
      })
      .catch((error) => {
        alert("Error updating project: " + error.message);
      });
  } else {
    store
      .createProject(editedItem.value)
      .then((response) => {
        close();
        alert("Project created successfully!");
        emits("refreshProjects");
      })
      .catch((error) => {
        alert("Error creating project: " + error.message);
      });
  }
}
</script>
