import api from "@/lib/api";
import { User } from "@/types/user";

export const userService = {
  getAll: async (): Promise<User[]> => {
    const response = await api.get<any>("/users");
    let responseData = response.data;

    if (typeof responseData === 'string') {
      let stringToParse = responseData;
      if (stringToParse.startsWith('+')) {
        stringToParse = stringToParse.substring(1);
      }
      try {
        responseData = JSON.parse(stringToParse);
      } catch (error) {
        console.error('[userService.getAll] Failed to parse JSON string:', error, 'Attempted to parse:', stringToParse);
        throw new Error("Failed to parse user data string from API");
      }
    }

    if (responseData && typeof responseData === 'object' && responseData.hasOwnProperty('data') && Array.isArray(responseData.data)) {
      const transformedUsers = responseData.data.map((apiUser: any) => {
        const user = {
          ...apiUser,
          active: apiUser.active === 1,
        };
        return user;
      });
      return transformedUsers as User[];
    } else if (Array.isArray(responseData)) {
      const transformedUsers = responseData.map((apiUser: any) => {
        const user = {
          ...apiUser,
          active: apiUser.active === 1,
        };
        return user;
      });
      return transformedUsers as User[];
    } else {
      console.error('[userService.getAll] Unexpected response structure after processing:', responseData);
      throw new Error("Unexpected user data structure from API after processing");
    }
  },
  
  getById: async (id: string): Promise<User> => {
    const response = await api.get<User>(`/users/${id}`);
    return response.data;
  },
  
  create: async (user: Omit<User, "id">): Promise<User> => {
    const response = await api.post<User>("/users", user);
    return response.data;
  },
  
  update: async (user: User): Promise<User> => {
    const endpoint = `/users/${user.id}`;
    try {
      const response = await api.put<User>(endpoint, user);
      return response.data;
    } catch (error) {
      throw error;
    }
  },
  
  delete: async (id: string): Promise<void> => {
    await api.delete(`/users/${id}`);
  }
};
