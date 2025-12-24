<template>
  <div class="roadmap-app">
    <component is="style" v-if="tasksData.length">{{ dynamicGanttStyles }}</component>

    <div class="main-card">
      <div class="header-area">
        <div class="title-group">
          <h2 class="title-text">Task Flow & Roadmap</h2>
          <p class="subtitle-text">Real-time Synchronization</p>
        </div>
        
        <div class="action-group">
          <div class="mode-pills">
            <button v-for="mode in ['Day', 'Week']" :key="mode" @click="changeViewMode(mode)"
              :class="['pill-btn', { active: viewMode === mode }]">{{ mode }}</button>
          </div>
          <button @click="scrollToToday" class="today-btn">Today</button>
        </div>
      </div>

      <div class="legend-card">
        <div class="legend-flex">
          <div class="legend-section">
            <span class="label">STATUS:</span>
            <div class="items">
              <div v-for="s in legendData.statuses" :key="s.name" class="item">
                <span class="dot" :style="{ backgroundColor: s.color }"></span>
                <span class="text">{{ s.name }}</span>
              </div>
              <span v-if="!legendData.statuses.length" class="no-data">(No status used)</span>
            </div>
          </div>
          <div class="legend-divider"></div>
          <div class="legend-section">
            <span class="label">PRIORITY:</span>
            <div class="items">
              <div v-for="p in legendData.priorities" :key="p.name" class="item">
                <span class="outline-box" :style="{ borderColor: p.color }"></span>
                <span class="text">{{ p.name }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="gantt-outer">
        <div v-if="loading" class="loader-overlay"><div class="spinner"></div></div>
        <div class="gantt-scroll-box" ref="scrollBox" 
             @mousedown="onDragStart" @mousemove="onDragMove" @mouseup="onDragEnd" @mouseleave="onDragEnd">
          <div ref="ganttContainer" class="frappe-gantt-custom"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import axios from 'axios';
import Gantt from 'frappe-gantt';

const props = defineProps({ projectId: { type: String, required: true } });
const ganttContainer = ref(null);
const scrollBox = ref(null);
const tasksData = ref([]);
const legendData = ref({ statuses: [], priorities: [] });
const loading = ref(true);
const viewMode = ref('Week');
let ganttInstance = null;

// Logic Kéo chuột để cuộn
const isDragging = ref(false);
const startX = ref(0);
const scrollLeftStart = ref(0);

const onDragStart = (e) => {
  isDragging.value = true;
  startX.value = e.pageX - scrollBox.value.offsetLeft;
  scrollLeftStart.value = scrollBox.value.scrollLeft;
};
const onDragEnd = () => isDragging.value = false;
const onDragMove = (e) => {
  if (!isDragging.value) return;
  const x = e.pageX - scrollBox.value.offsetLeft;
  const walk = (x - startX.value) * 1.5;
  scrollBox.value.scrollLeft = scrollLeftStart.value - walk;
};

const dynamicGanttStyles = computed(() => {
  return tasksData.value.map(task => `
    .gantt g[data-id="${task.id}"] .bar-progress { fill: ${task.status_color} !important; opacity: 0.9; }
    .gantt g[data-id="${task.id}"] .bar { 
      stroke: ${task.priority_color} !important; 
      stroke-width: 1.5px !important; 
      fill: #ffffff !important;
      rx: 4px; ry: 4px;
    }
  `).join('\n');
});

const scrollToToday = () => {
  const todayHighlight = document.querySelector('.today-highlight');
  if (todayHighlight && scrollBox.value) {
    todayHighlight.scrollIntoView({ behavior: 'smooth', inline: 'center' });
  }
};

const fetchAndRenderGantt = async () => {
  loading.value = true;
  try {
    const response = await axios.get(`/api/projects/${props.projectId}/roadmap`);
    tasksData.value = response.data.tasks;
    legendData.value = response.data.legend;
    loading.value = false;

    await nextTick();

    const options = {
      view_mode: viewMode.value,
      bar_height: 22, // Mảnh hơn để vừa khung hình
      padding: 14,
      header_height: 40,
      column_width: viewMode.value === 'Day' ? 25 : 85,
      bar_corner_radius: 4,
      custom_popup_html: (task) => `<div class="mini-popup">${task.name}</div>`
    };

    if (ganttInstance) {
      ganttInstance.refresh(tasksData.value);
    } else {
      ganttInstance = new Gantt(ganttContainer.value, tasksData.value, options);
    }
    
    setTimeout(scrollToToday, 500);
  } catch (err) { console.error(err); loading.value = false; }
};

const changeViewMode = (mode) => {
  viewMode.value = mode;
  if (ganttInstance) {
    ganttInstance.options.column_width = mode === 'Day' ? 25 : 85;
    ganttInstance.change_view_mode(mode);
    setTimeout(scrollToToday, 300);
  }
};

onMounted(fetchAndRenderGantt);
</script>

<style scoped>
/* Sử dụng font Inter cho nét chữ mảnh và hiện đại */
.roadmap-app { 
  font-family: 'Inter', -apple-system, system-ui, sans-serif; 
  padding: 10px; 
  background: #f8fafc; 
}

.main-card { 
  background: white; border-radius: 12px; border: 1px solid #e2e8f0; 
  box-shadow: 0 1px 4px rgba(0,0,0,0.02); overflow: hidden; 
}

.header-area { 
  padding: 12px 20px; display: flex; justify-content: space-between; 
  align-items: center; border-bottom: 1px solid #f1f5f9; 
}
.title-text { font-size: 16px; font-weight: 700; color: #1e293b; margin: 0; }
.subtitle-text { font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }

.mode-pills { background: #f1f5f9; padding: 2px; border-radius: 8px; display: flex; }
.pill-btn { 
  padding: 4px 12px; border-radius: 6px; border: none; font-size: 11px; 
  font-weight: 600; color: #64748b; cursor: pointer; background: transparent; 
}
.pill-btn.active { background: white; color: #3b82f6; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.today-btn { 
  background: #3b82f6; color: white; border: none; padding: 5px 12px; 
  border-radius: 6px; font-size: 11px; font-weight: 600; cursor: pointer; margin-left: 8px; 
}

/* Legend gọn gàng */
.legend-card { padding: 8px 20px; border-bottom: 1px solid #f1f5f9; background: #fff; }
.legend-flex { display: flex; align-items: center; gap: 20px; }
.legend-section { display: flex; align-items: center; gap: 8px; }
.label { font-size: 9px; font-weight: 800; color: #cbd5e1; letter-spacing: 0.5px; }
.items { display: flex; align-items: center; gap: 12px; }
.item { display: flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; color: #475569; }
.dot { width: 8px; height: 8px; border-radius: 2px; }
.outline-box { width: 16px; height: 8px; border: 1.5px solid; border-radius: 2px; background: #fff; }
.legend-divider { width: 1px; height: 14px; background: #e2e8f0; }
.no-data { font-size: 10px; color: #cbd5e1; }

.gantt-outer { position: relative; min-height: 400px; }
.gantt-scroll-box { overflow-x: auto; cursor: grab; }
.gantt-scroll-box:active { cursor: grabbing; }

.loader-overlay { position: absolute; inset: 0; background: rgba(255,255,255,0.8); z-index: 10; display: flex; justify-content: center; align-items: center; }
.spinner { width: 20px; height: 20px; border: 2px solid #f1f5f9; border-top-color: #3b82f6; border-radius: 50%; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>

<style>
/* CSS Global cho Frappe Gantt - Nét vẽ siêu mảnh */
.gantt .grid-header { fill: #ffffff; stroke: #f1f5f9; }
.gantt .upper-text { fill: #94a3b8; font-size: 9px; font-weight: 700; text-transform: uppercase; }
.gantt .lower-text { fill: #64748b; font-weight: 600; font-size: 10px; }
.gantt .bar-label { fill: #1e293b !important; font-weight: 600 !important; font-size: 10px !important; }
.gantt .today-highlight { fill: rgba(59, 130, 246, 0.02); stroke: #3b82f6; stroke-width: 1; stroke-dasharray: 4,4; }
.mini-popup { background: #1e293b; color: white; padding: 4px 8px; border-radius: 4px; font-size: 10px; }
</style>