import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: () => import("../layouts/FullLayout.vue"),
      children: [
        {
          name: "dashboard",
          path: "/",
          component: () => import("../views/Dashboard.vue"),
        },
        {
          name: "project",
          path: "/project/:projectId",
          component: () => import("../views/Project.vue"),
        },
        {
          name: "job-details",
          path: "/job-details/:jobExecutionId?",
          component: () => import("../views/JobDetails.vue"),
        },
        {
          name: "execute-job",
          path: "/execute-job/:projectId?",
          component: () => import("../views/ExecuteJob.vue"),
        },
      ],
    },
  ],
});

export default router;
