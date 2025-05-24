import api from "@/lib/api";
import { EntryForm } from "@/types/entry-form";

export const entryFormService = {
  getAll: async (): Promise<EntryForm[]> => { // Note: Return type will be changed later
    const cacheBuster = new Date().getTime();
    const url = `/entry-forms?_cb=${cacheBuster}`;
    console.log(`[entryFormService.getAll] Fetching: ${url}`);
    const response = await api.get<any>(url);
    console.log('[entryFormService.getAll] Raw API Response object:', JSON.stringify(response)); // Log the whole object stringified
    
    const responseData = response.data; // Work with a clearly scoped variable
    console.log('[entryFormService.getAll] responseData (type ' + typeof responseData + '):', JSON.stringify(responseData));

    if (responseData && typeof responseData === 'object' && responseData.data && Array.isArray(responseData.data) && responseData.meta && typeof responseData.meta.total !== 'undefined') {
      console.log(`[entryFormService.getAll] Detected PAGINATED structure. responseData.meta.total = ${responseData.meta.total}. Returning responseData.data (length: ${responseData.data.length})`);
      return responseData.data as EntryForm[];
    } else if (Array.isArray(responseData)) {
      console.log(`[entryFormService.getAll] Detected FLAT ARRAY structure. Length: ${responseData.length}. Returning as is.`);
      return responseData as EntryForm[];
    } else {
      console.error('[entryFormService.getAll] UNEXPECTED responseData structure:', responseData);
      throw new Error('Unexpected data structure received from entry-forms API.');
    }
  },
  
  getById: async (id: string): Promise<EntryForm> => {
    const response = await api.get<EntryForm>(`/entry-forms/${id}`);
    return response.data;
  },
  
  create: async (entryForm: Omit<EntryForm, "id">): Promise<EntryForm> => {
    const response = await api.post<EntryForm>("/entry-forms", entryForm);
    return response.data;
  },
  
  delete: async (id: string): Promise<void> => {
    await api.delete(`/entry-forms/${id}`);
  }
};
