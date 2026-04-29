<template>
  <div class="opt1">
    <div style="display:flex; height: 100vh;">
      
      <div class="opt1-sidebar">
        <div class="opt1-logo">
          <div class="opt1-logo-text">AdminPanel</div>
          <div class="opt1-logo-sub">v1.0 — full-stack</div>
        </div>
        <div class="opt1-nav-item active"><div class="opt1-nav-dot" style="background:#4f6ef7"></div>Dashboard</div>
        <div class="opt1-nav-item"><div class="opt1-nav-dot"></div>Usuários</div>
        <div class="opt1-nav-item"><div class="opt1-nav-dot"></div>Produtos</div>
        <div class="opt1-nav-item"><div class="opt1-nav-dot"></div>Relatórios</div>
        <div style="flex:1"></div>
        
        <div class="opt1-nav-item logout-btn" @click="handleLogout">
          <div class="opt1-nav-dot" style="background:#ef4444"></div>Sair
        </div>
      </div>

      <div class="opt1-main">
        <div class="opt1-header">
          <div>
            <div class="opt1-title">Usuários</div>
            <div style="font-size:12px;color:#555;margin-top:2px;">Gerencie todos os usuários do sistema</div>
          </div>
          <button class="opt1-btn" @click="openNewUserModal">+ Novo usuário</button>
        </div>
        
        <div class="opt1-stats">
          <div class="opt1-stat">
            <div class="opt1-stat-label">Total usuários</div>
            <div class="opt1-stat-val">248</div>
            <div class="opt1-stat-change">+12 este mês</div>
          </div>
          <div class="opt1-stat">
            <div class="opt1-stat-label">Ativos</div>
            <div class="opt1-stat-val">201</div>
            <div class="opt1-stat-change" style="color:#4f6ef7">81% do total</div>
          </div>
          <div class="opt1-stat">
            <div class="opt1-stat-label">Produtos</div>
            <div class="opt1-stat-val">1.4k</div>
            <div class="opt1-stat-change">~5.7 por usuário</div>
          </div>
        </div>

        <div class="opt1-table-wrap">
          <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid #2a2d38;">
            <input style="background:#0f1117;border:1px solid #2a2d38;border-radius:6px;padding:6px 12px;color:#ccc;font-size:12px;width:200px;" placeholder="Buscar usuário..." />
            <div style="display:flex;gap:8px;">
              <select style="background:#0f1117;border:1px solid #2a2d38;border-radius:6px;padding:6px 10px;color:#888;font-size:12px;">
                <option>Todos</option><option>Ativos</option><option>Inativos</option>
              </select>
            </div>
          </div>
          
          <table class="opt1-table">
            <thead>
              <tr>
                <th>Nome</th><th>Email</th><th>CPF</th><th>Produtos</th><th>Status</th><th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoading">
                <td colspan="6" style="text-align:center; padding: 20px; color: #888;">
                  Carregando dados do servidor...
                </td>
              </tr>
              
              <tr v-else v-for="user in usuarios" :key="user?.id">
                <td>
                  <div style="display:flex;align-items:center;gap:8px;">
                    <div class="opt1-avatar" style="background:#4f6ef7">
                      {{ getInitials(user?.nome) }}
                    </div>
                    {{ user.nome }}
                  </div>
                </td>
                <td style="color:#888">{{ user.email }}</td>
                <td style="font-family:'DM Mono',monospace;font-size:12px;color:#666">{{ user.cpf }}</td>
                <td><span style="color:#4f6ef7;font-weight:500">-</span></td>
                <td><span class="opt1-badge active">ativo</span></td>
                <td>
                  <div style="display:flex;gap:8px;">
                    <button class="action-btn">Ver</button>
                    <button class="action-btn" @click="openEditUserModal(user)">Editar</button>
                    <button class="action-btn" style="color: #ef4444; border-color: #5c1a1a;" @click="excluirUsuario(user.id)">Excluir</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <v-dialog v-model="showModal" max-width="500px">
      <v-card style="background: #16181f; border: 1px solid #2a2d38; color: #fff;">
        <v-card-title class="pa-4" style="border-bottom: 1px solid #2a2d38; font-family: 'DM Sans', sans-serif;">
          Novo Usuário
        </v-card-title>
        
        <v-card-text class="pa-4">
          <v-alert v-if="formError" type="error" variant="tonal" class="mb-4 text-body-2">
            {{ formError }}
          </v-alert>

          <div style="font-size: 13px; color: #888; margin-bottom: 4px;">Nome completo</div>
          <v-text-field v-model="formData.nome" variant="outlined" density="compact" bg-color="#0f1117" color="primary"></v-text-field>

          <div style="font-size: 13px; color: #888; margin-bottom: 4px;">E-mail</div>
          <v-text-field v-model="formData.email" type="email" variant="outlined" density="compact" bg-color="#0f1117" color="primary"></v-text-field>

          <div style="font-size: 13px; color: #888; margin-bottom: 4px;">CPF (000.000.000-00)</div>
          <v-text-field v-model="formData.cpf" variant="outlined" density="compact" bg-color="#0f1117" color="primary"></v-text-field>

          <div style="font-size: 13px; color: #888; margin-bottom: 4px;">Senha (mín. 6 caracteres)</div>
          <v-text-field v-model="formData.senha" type="password" variant="outlined" density="compact" bg-color="#0f1117" color="primary"></v-text-field>
        </v-card-text>

        <v-card-actions class="pa-4" style="border-top: 1px solid #2a2d38;">
          <v-spacer></v-spacer>
          <button @click="showModal = false" style="background:none; border:1px solid #2a2d38; color:#888; padding:8px 16px; border-radius:8px; cursor:pointer; margin-right: 8px;">
            Cancelar
          </button>
          <button @click="salvarUsuario" class="opt1-btn" :disabled="isSaving" :style="isSaving ? 'opacity: 0.5' : ''">
            {{ isSaving ? 'Salvando...' : 'Salvar Usuário' }}
          </button>
        </v-card-actions>
      </v-card>
    </v-dialog>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import http from '../services/http';

const router = useRouter();
const authStore = useAuthStore();
interface User {
  id: number;
  nome: string;
  email: string;
  cpf: string;
}

const usuarios = ref<User[]>([]);
const totalUsuarios = ref(0);
const isLoading = ref(true);

const fetchUsuarios = async () => {
  try {
    const response = await http.get('/users');
    usuarios.value = response.data.data; 
    totalUsuarios.value = response.data.total;
  } catch (error) {
    console.error('Erro ao buscar usuários:', error);
  } finally {
    isLoading.value = false;
  }
};

const getInitials = (nome: string | null | undefined) => {
  if (!nome) return '--'; 
  
  const nomes = nome.trim().split(' ');
  if (nomes.length >= 2) {
    const primeiraLetra = nomes[0]?.[0] ?? '';
    const segundaLetra = nomes[1]?.[0] ?? '';
    return `${primeiraLetra}${segundaLetra}`.toUpperCase() || '--';
  }
  return nome.substring(0, 2).toUpperCase();
};

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};

const showModal = ref(false);
const isSaving = ref(false);
const formError = ref('');
const editingId = ref<number | null>(null);

const formData = ref({
  nome: '',
  email: '',
  cpf: '',
  senha: ''
});

const openNewUserModal = () => {
  editingId.value = null; // Garante que é uma criação
  formData.value = { nome: '', email: '', cpf: '', senha: '' };
  formError.value = '';
  showModal.value = true;
};

const openEditUserModal = (user: User) => {
  editingId.value = user.id;
  // Preenche os dados, mas deixa a senha em branco (o back-end ignora se vier vazia)
  formData.value = { nome: user.nome, email: user.email, cpf: user.cpf, senha: '' };
  formError.value = '';
  showModal.value = true;
};

const salvarUsuario = async () => {
  isSaving.value = true;
  formError.value = '';
  
  try {
    if (editingId.value) {
      await http.put(`/users/${editingId.value}`, formData.value);
    } else {
      await http.post('/users', formData.value);
    }
    
    showModal.value = false;
    fetchUsuarios();
  } catch (error: any) {
    if (error.response?.status === 422) {
        const errors = error.response.data.errors as Record<string, string[]>;
        const firstError = Object.values(errors)[0]?.[0];
        formError.value = firstError ?? 'Não foi possível salvar o usuário.';
    } else {
        formError.value = 'Erro inesperado ao salvar o usuário.';
    }
  } finally {
    isSaving.value = false;
  }
};

const excluirUsuario = async (id: number) => {
  if (confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')) {
    try {
      await http.delete(`/users/${id}`);
      fetchUsuarios(); // Recarrega a tabela
    } catch (error) {
      console.error('Erro ao excluir usuário:', error);
      alert('Não foi possível excluir o usuário.');
    }
  }
};

onMounted(() => {
  fetchUsuarios();
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=DM+Mono:wght@400;500&display=swap');

.opt1 * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'DM Sans', sans-serif; }
.opt1 { background: #0f1117; color: #e8e8e8; overflow: hidden; height: 100vh; width: 100vw; }
.opt1-sidebar { width: 220px; background: #16181f; border-right: 1px solid #2a2d38; padding: 20px 0; display: flex; flex-direction: column; }
.opt1-logo { padding: 0 20px 20px; border-bottom: 1px solid #2a2d38; margin-bottom: 12px; }
.opt1-logo-text { font-size: 16px; font-weight: 600; color: #fff; letter-spacing: -0.3px; }
.opt1-logo-sub { font-size: 11px; color: #555; font-family: 'DM Mono', monospace; margin-top: 2px; }
.opt1-nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 20px; font-size: 13px; color: #888; cursor: pointer; transition: all 0.15s; }
.opt1-nav-item:hover { color: #e8e8e8; background: #1e2029; }
.opt1-nav-item.active { color: #fff; background: #22253a; border-right: 2px solid #4f6ef7; }
.opt1-nav-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
.opt1-main { flex: 1; padding: 24px; overflow: hidden; }
.opt1-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.opt1-title { font-size: 20px; font-weight: 600; color: #fff; }
.opt1-btn { background: #4f6ef7; color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; }
.opt1-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px; }
.opt1-stat { background: #16181f; border: 1px solid #2a2d38; border-radius: 10px; padding: 16px; }
.opt1-stat-label { font-size: 11px; color: #555; font-family: 'DM Mono', monospace; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
.opt1-stat-val { font-size: 22px; font-weight: 600; color: #fff; }
.opt1-stat-change { font-size: 11px; color: #4ade80; margin-top: 2px; }
.opt1-table-wrap { background: #16181f; border: 1px solid #2a2d38; border-radius: 10px; overflow: hidden; }
.opt1-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.opt1-table th { padding: 12px 16px; text-align: left; font-size: 11px; color: #555; font-family: 'DM Mono', monospace; border-bottom: 1px solid #2a2d38; font-weight: 500; text-transform: uppercase; }
.opt1-table td { padding: 12px 16px; color: #ccc; border-bottom: 1px solid #1e2029; }
.opt1-table tr:last-child td { border-bottom: none; }
.opt1-badge { display: inline-block; font-size: 10px; padding: 2px 8px; border-radius: 4px; font-family: 'DM Mono', monospace; }
.opt1-badge.active { background: #0d2a1a; color: #4ade80; }
.opt1-badge.inactive { background: #2a1a0d; color: #f97316; }
.opt1-avatar { width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 600; color: #fff; }

.logout-btn {
  border-top: 1px solid #2a2d38; 
  margin-top: 8px;
}
.logout-btn:hover {
  color: #ef4444; /* Fica vermelho ao passar o mouse */
}
.action-btn {
  background: none; 
  border: 1px solid #2a2d38; 
  color: #888; 
  padding: 4px 10px; 
  border-radius: 5px; 
  font-size: 11px; 
  cursor: pointer;
}
.action-btn:hover {
  background: #1e2029;
  color: #fff;
}
</style>