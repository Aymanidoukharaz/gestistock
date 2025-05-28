import api from "@/lib/api";
import { ExitForm } from "@/types/exit-form";

export const exitFormService = {
  getAll: async (): Promise<ExitForm[]> => {
    const response = await api.get<any>("/exit-forms");
    let responseData = response.data;

    if (typeof responseData === 'string' && responseData.startsWith('+')) {
      try {
        responseData = JSON.parse(responseData.substring(1));
      } catch (e) {
        throw new Error("Failed to parse exit forms response after stripping '+'.");
      }
    }

    if (responseData && typeof responseData === 'object' && Array.isArray(responseData.data)) {
      return responseData.data as ExitForm[];
    } else if (Array.isArray(responseData)) {
      return responseData as ExitForm[];
    } else {
      throw new Error("Unexpected response structure for exit forms.");
    }
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
  },

  completeExitForm: async (id: string): Promise<ExitForm> => {
    const response = await api.post<any>(`/exit-forms/${id}/validate`, { validation_note: 'Completed by user action' });
    let responseData = response.data;
    if (typeof responseData === 'string' && responseData.startsWith('+')) {
      try {
        responseData = JSON.parse(responseData.substring(1));
      } catch (e) {
        throw new Error('Failed to parse API response for completeExitForm.');
      }
    }
    return responseData.data;
  },

  cancelExitForm: async (id: string): Promise<ExitForm> => {
    const response = await api.post<any>(`/exit-forms/${id}/cancel`, { reason: 'Cancelled by user action' });
    let responseData = response.data;
    if (typeof responseData === 'string' && responseData.startsWith('+')) {
      try {
        responseData = JSON.parse(responseData.substring(1));
      } catch (e) {
        throw new Error('Failed to parse API response for cancelExitForm.');
      }
    }
    return responseData.data;
  }
};
