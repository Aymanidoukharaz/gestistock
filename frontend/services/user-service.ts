import api from "@/lib/api";
import { User } from "@/types/user";

export const userService = {
  getAll: async (): Promise<User[]> => {
    const response = await api.get<User[]>("/users");
    return response.data;
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
    const response = await api.put<User>(`/users/${user.id}`, user);
    return response.data;
  },
  
  delete: async (id: string): Promise<void> => {
    await api.delete(`/users/${id}`);
  }
};
