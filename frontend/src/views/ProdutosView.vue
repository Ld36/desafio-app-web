<template>
  <div class="opt1">
    <div style="display:flex; height: 100vh;">
      
      <div class="opt1-sidebar">
        <div class="opt1-logo">
          <div class="opt1-logo-text">AdminPanel</div>
          <div class="opt1-logo-sub">v1.0 — full-stack</div>
        </div>
        <div class="opt1-nav-item" @click="$router.push('/')"><div class="opt1-nav-dot"></div>Dashboard</div>
        <div class="opt1-nav-item" @click="$router.push('/')"><div class="opt1-nav-dot"></div>Usuários</div>
        <div class="opt1-nav-item active"><div class="opt1-nav-dot" style="background:#4f6ef7"></div>Produtos</div>
        <div class="opt1-nav-item"><div class="opt1-nav-dot"></div>Relatórios</div>
        <div style="flex:1"></div>
        
        <div class="opt1-nav-item logout-btn" @click="handleLogout">
          <div class="opt1-nav-dot" style="background:#ef4444"></div>Sair
        </div>
      </div>

      <div class="opt1-main">
        <div class="opt1-header">
          <div>
            <div class="opt1-title">Produtos</div>
            <div style="font-size:12px;color:#555;margin-top:2px;">Gerencie o catálogo de produtos do sistema</div>
          </div>
          <button class="opt1-btn" @click="openNewProductModal">+ Novo produto</button>
        </div>

        <div class="opt1-table-wrap mt-6">
          <table class="opt1-table">
            <thead>
              <tr>
                <th>Produto</th>
                <th>Preço (R$)</th>
                <th>Descrição</th>
                <th>Dono (Usuário)</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoading">
                <td colspan="5" style="text-align:center; padding: 20px; color: #888;">
                  Carregando produtos...
                </td>
              </tr>
              <tr v-else-if="produtos.length === 0">
                <td colspan="5" style="text-align:center; padding: 20px; color: #888;">
                  Nenhum produto cadastrado.
                </td>
              </tr>
              <tr v-else v-for="produto in produtos" :key="produto.id">
                <td style="color:#fff; font-weight: 500;">{{ produto.nome }}</td>
                <td style="color:#4ade80; font-family:'DM Mono',monospace;">R$ {{ produto.preco }}</td>
                <td style="color:#888">{{ produto.descricao || 'Sem descrição' }}</td>
                <td>
                  <span style="background: #22253a; color: #4f6ef7; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                    {{ produto.user?.nome || 'Desconhecido' }}
                  </span>
                </td>
                <td>
                  <div style="display:flex;gap:8px;">
                    <button class="action-btn" @click="openEditProductModal(produto)">Editar</button>
                    <button class="action-btn" style="color: #ef4444; border-color: #5c1a1a;" @click="excluirProduto(produto.id)">Excluir</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <v-dialog v-model="showModal" max-width="500px">
      <v-card style="background: #16181f; border: 1px solid #2a2d38; color: #fff;">
        <v-card-title class="pa-4" style="border-bottom: 1px solid #2a2d38; font-family: 'DM Sans', sans-serif;">
          {{ editingId ? 'Editar Produto' : 'Novo Produto' }}
        </v-card-title>
        
        <v-card-text class="pa-4">
          <v-alert v-if="formError" type="error" variant="tonal" class="mb-4 text-body-2">{{ formError }}</v-alert>

          <div style="font-size: 13px; color: #888; margin-bottom: 4px;">Nome do Produto</div>
          <v-text-field v-model="formData.nome" variant="outlined" density="compact" bg-color="#0f1117" color="primary"></v-text-field>

          <div style="font-size: 13px; color: #888; margin-bottom: 4px;">Preço (R$)</div>
          <v-text-field v-model="formData.preco" type="number" step="0.01" variant="outlined" density="compact" bg-color="#0f1117" color="primary"></v-text-field>

          <div style="font-size: 13px; color: #888; margin-bottom: 4px;">Descrição (Opcional)</div>
          <v-textarea v-model="formData.descricao" variant="outlined" density="compact" bg-color="#0f1117" color="primary" rows="2"></v-textarea>

          <div style="font-size: 13px; color: #888; margin-bottom: 4px;">Vincular ao Usuário</div>
          <v-select
            v-model="formData.user_id"
            :items="usuariosList"
            item-title="nome"
            item-value="id"
            variant="outlined"
            density="compact"
            bg-color="#0f1117"
            color="primary"
            placeholder="Selecione um dono"
          ></v-select>
        </v-card-text>

        <v-card-actions class="pa-4" style="border-top: 1px solid #2a2d38;">
          <v-spacer></v-spacer>
          <button @click="showModal = false" style="background:none; border:1px solid #2a2d38; color:#888; padding:8px 16px; border-radius:8px; cursor:pointer; margin-right: 8px;">Cancelar</button>
          <button @click="salvarProduto" class="opt1-btn" :disabled="isSaving" :style="isSaving ? 'opacity: 0.5' : ''">
            {{ isSaving ? 'Salvando...' : 'Salvar Produto' }}
          </button>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import http from '../services/http';

const router = useRouter();
const authStore = useAuthStore();

interface Produto {
  id: number;
  nome: string;
  preco: number;
  descricao: string;
  user_id: number;
  user?: { nome: string }; // Relacionamento que vem do Laravel
}

const produtos = ref<Produto[]>([]);
const usuariosList = ref<{id: number, nome: string}[]>([]);
const isLoading = ref(true);

const showModal = ref(false);
const isSaving = ref(false);
const formError = ref('');
const editingId = ref<number | null>(null);

const formData = ref({
  nome: '',
  preco: '',
  descricao: '',
  user_id: null as number | null
});

const fetchData = async () => {
  isLoading.value = true;
  try {
    const [prodRes, userRes] = await Promise.all([
      http.get('/products'),
      http.get('/users') // Pegamos os usuários para preencher o Vuetify Select
    ]);
    produtos.value = prodRes.data.data;
    usuariosList.value = userRes.data.data;
  } catch (error) {
    console.error('Erro ao buscar dados:', error);
  } finally {
    isLoading.value = false;
  }
};

const openNewProductModal = () => {
  editingId.value = null;
  formData.value = { nome: '', preco: '', descricao: '', user_id: null };
  formError.value = '';
  showModal.value = true;
};

const openEditProductModal = (produto: Produto) => {
  editingId.value = produto.id;
  formData.value = { 
    nome: produto.nome, 
    preco: produto.preco.toString(), 
    descricao: produto.descricao || '', 
    user_id: produto.user_id 
  };
  formError.value = '';
  showModal.value = true;
};

const salvarProduto = async () => {
  isSaving.value = true;
  formError.value = '';
  
  try {
    if (editingId.value) {
      await http.put(`/products/${editingId.value}`, formData.value);
    } else {
      await http.post('/products', formData.value);
    }
    showModal.value = false;
    fetchData(); // Recarrega a tabela
  } catch (error: any) {
    if (error.response?.status === 422) {
        const errors = error.response.data.errors as Record<string, string[]>;
        formError.value = Object.values(errors)[0]?.[0] ?? 'Erro de validação.';
    } else {
        formError.value = 'Erro inesperado ao salvar o produto.';
    }
  } finally {
    isSaving.value = false;
  }
};

const excluirProduto = async (id: number) => {
  if (confirm('Tem certeza que deseja excluir este produto?')) {
    try {
      await http.delete(`/products/${id}`);
      fetchData();
    } catch (error) {
      alert('Erro ao excluir.');
    }
  }
};

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};

onMounted(() => {
  fetchData();
});
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=DM+Mono:wght@400;500&display=swap');

/* Mantendo seu CSS original exatamente igual */
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
.opt1-table-wrap { background: #16181f; border: 1px solid #2a2d38; border-radius: 10px; overflow: hidden; }
.opt1-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.opt1-table th { padding: 12px 16px; text-align: left; font-size: 11px; color: #555; font-family: 'DM Mono', monospace; border-bottom: 1px solid #2a2d38; font-weight: 500; text-transform: uppercase; }
.opt1-table td { padding: 12px 16px; color: #ccc; border-bottom: 1px solid #1e2029; }
.opt1-table tr:last-child td { border-bottom: none; }

.logout-btn { border-top: 1px solid #2a2d38; margin-top: 8px; }
.logout-btn:hover { color: #ef4444; }
.action-btn { background: none; border: 1px solid #2a2d38; color: #888; padding: 4px 10px; border-radius: 5px; font-size: 11px; cursor: pointer; }
.action-btn:hover { background: #1e2029; color: #fff; }
</style>