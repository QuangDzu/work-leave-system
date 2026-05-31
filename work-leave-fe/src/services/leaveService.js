import api from "./api";

export const leaveService = {
  list: (params = {}) => api.get("/leaves", { params }).then((r) => r.data),
  get: (id) => api.get(`/leaves/${id}`).then((r) => r.data),
  create: (data) => api.post("/leaves", data).then((r) => r.data),
  update: (id, data) => api.put(`/leaves/${id}`, data).then((r) => r.data),
  delete: (id) => api.delete(`/leaves/${id}`).then((r) => r.data),
  approve: (id) => api.put(`/leaves/${id}/approve`).then((r) => r.data),
  reject: (id) => api.put(`/leaves/${id}/reject`).then((r) => r.data),
  cancel: (id) => api.put(`/leaves/${id}/cancel`).then((r) => r.data),
};
