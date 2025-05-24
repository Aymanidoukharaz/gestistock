import api from "@/lib/api";
import { ExitForm } from "@/types/exit-form";

export const exitFormService = {
  getAll: async (): Promise<ExitForm[]> => {
    const response = await api.get<ExitForm[]>("/exit-forms");
    return response.data;
  },
  
  getById: async (id: string): Promise<ExitForm> => {
    const response = await api.get<ExitForm>(`/exit-forms/${id}`);
    return response.data;
  },
  
  create: async (exitForm: Omit<ExitForm, "id">): Promise<ExitForm> => {
    const response = await api.post<ExitForm>("/exit-forms", exitForm);
    return response.data;
  },
  
  delete: async (id: string): Promise<void> => {
    await api.delete(`/exit-forms/${id}`);
  }
};
