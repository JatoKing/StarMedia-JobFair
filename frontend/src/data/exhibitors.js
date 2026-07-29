// Dummy data exhibitor untuk Job Fair Directory
// TODO: gantikan dengan data sebenar dari API PHP (backend/api/exhibitor.php) bila backend siap
// NOTA: 'category' guna key asal (Teknologi, Kewangan, dll) — label displayed diterjemah melalui i18n
// dalam directory.categories.* (lihat DirectorySection.vue & ExhibitorCard.vue)

export const exhibitorCategoryKeys = [
  'Teknologi',
  'Kewangan',
  'Runcit',
  'Pembuatan',
  'Perkhidmatan'
]

const categoryColors = {
  Teknologi: '#6C5CE7',
  Kewangan: '#00D9C0',
  Runcit: '#FF6B35',
  Pembuatan: '#FFC93C',
  Perkhidmatan: '#FF4757'
}

export const exhibitors = [
  {
    id: 1,
    name: 'Nexora Technologies',
    category: 'Teknologi',
    booth: 'A1',
    positions: ['Software Engineer', 'DevOps Engineer', 'QA Analyst'],
    description: 'Syarikat pembangunan perisian dan cloud solutions untuk industri korporat.'
  },
  {
    id: 2,
    name: 'MapleTrust Bank',
    category: 'Kewangan',
    booth: 'A2',
    positions: ['Financial Analyst', 'Bank Teller', 'Risk Officer'],
    description: 'Institusi kewangan terkemuka menawarkan perkhidmatan perbankan dan pelaburan.'
  },
  {
    id: 3,
    name: 'UrbanMart Retail',
    category: 'Runcit',
    booth: 'A3',
    positions: ['Store Supervisor', 'Merchandiser', 'Customer Service'],
    description: 'Rangkaian pasar raya terbesar dengan cawangan di seluruh negara.'
  },
  {
    id: 4,
    name: 'Ferroline Industries',
    category: 'Pembuatan',
    booth: 'B1',
    positions: ['Production Executive', 'Quality Control', 'Logistics Coordinator'],
    description: 'Pengeluar komponen automotif dan mesin industri.'
  },
  {
    id: 5,
    name: 'BrightPath Consulting',
    category: 'Perkhidmatan',
    booth: 'B2',
    positions: ['Business Consultant', 'HR Executive', 'Project Manager'],
    description: 'Firma perundingan pengurusan dan pembangunan organisasi.'
  },
  {
    id: 6,
    name: 'Skylume Data Systems',
    category: 'Teknologi',
    booth: 'B3',
    positions: ['Data Scientist', 'Backend Developer', 'IT Support'],
    description: 'Menyediakan penyelesaian big data dan analytics untuk enterprise.'
  },
  {
    id: 7,
    name: 'Coral Bay Finance',
    category: 'Kewangan',
    booth: 'C1',
    positions: ['Insurance Agent', 'Accounts Executive', 'Auditor'],
    description: 'Syarikat insurans dan pengurusan kewangan peribadi.'
  },
  {
    id: 8,
    name: 'Vantro Retail Group',
    category: 'Runcit',
    booth: 'C2',
    positions: ['Retail Manager', 'Visual Merchandiser', 'Sales Associate'],
    description: 'Kumpulan runcit fesyen dan lifestyle dengan kehadiran serantau.'
  },
  {
    id: 9,
    name: 'Ironclad Manufacturing',
    category: 'Pembuatan',
    booth: 'C3',
    positions: ['Mechanical Engineer', 'Machine Operator', 'Safety Officer'],
    description: 'Pembuat peralatan berat dan struktur keluli.'
  },
  {
    id: 10,
    name: 'Everline Solutions',
    category: 'Perkhidmatan',
    booth: 'D1',
    positions: ['Customer Success Manager', 'Admin Executive', 'Trainer'],
    description: 'Penyedia perkhidmatan outsourcing dan pengurusan pelanggan.'
  },
  {
    id: 11,
    name: 'Quanta Softworks',
    category: 'Teknologi',
    booth: 'D2',
    positions: ['Frontend Developer', 'UI/UX Designer', 'Product Manager'],
    description: 'Studio pembangunan aplikasi web dan mobile.'
  },
  {
    id: 12,
    name: 'Harbor Point Bank',
    category: 'Kewangan',
    booth: 'D3',
    positions: ['Credit Analyst', 'Branch Manager', 'Compliance Officer'],
    description: 'Bank komersial dengan fokus pembiayaan perniagaan kecil.'
  }
]

export function getCategoryColor(category) {
  return categoryColors[category] || '#6C5CE7'
}