let artikelPage = 1;

document.addEventListener("DOMContentLoaded", () => {
    if (document.getElementById("tabelArtikelAdmin")) loadArtikelAdmin();
    if (document.getElementById("tabelKategoriArtikel"))
        loadKategoriArtikelAdmin();
    if (document.getElementById("fKategori")) loadKategoriOptions();
    if (document.getElementById("fTipe")) toggleVideoUrl();
    document
        .getElementById("fVideoUrl")
        ?.addEventListener("input", updateThumbnailFromVideoUrl);
    document
        .getElementById("fThumbnail")
        ?.addEventListener("blur", function () {
            if (!this.readOnly)
                this.value = normalizeThumbnailUrl(this.value.trim());
        });
});

// =====================
// KELOLA KATEGORI
// =====================
async function loadKategoriArtikelAdmin() {
    const tbody = document.getElementById("tabelKategoriArtikel");
    try {
        const res = await fetch("/api/artikel-kategori/manage");
        const data = await res.json();
        const items = data.items || [];

        tbody.innerHTML = items.length
            ? items
                  .map(
                      (k, i) => `
        <tr>
          <td class="text-muted small">${i + 1}</td>
          <td style="font-weight:600">${k.nama}</td>
          <td class="text-muted small">${k.jumlah_artikel} artikel</td>
          <td>
            <div class="d-flex gap-1">
              <button class="btn btn-admin-edit btn-sm" onclick="renameKategoriArtikel(${k.id}, '${k.nama.replace(/'/g, "\\'")}')"><i class="bi bi-pencil"></i></button>
              <button class="btn btn-admin-danger btn-sm" onclick="hapusKategoriArtikel(${k.id}, '${k.nama.replace(/'/g, "\\'")}')"><i class="bi bi-trash"></i></button>
            </div>
          </td>
        </tr>`,
                  )
                  .join("")
            : `<tr><td colspan="4" class="text-center text-muted py-4">Belum ada kategori.</td></tr>`;
    } catch {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">Gagal memuat data.</td></tr>`;
    }
}

function showKategoriAlert(type, msg) {
    const el = document.getElementById("kategoriAlert");
    if (!el) return;
    el.className = `alert alert-${type} py-2 small`;
    el.textContent = msg;
    el.classList.remove("d-none");
}

async function tambahKategoriArtikel() {
    const nama = document.getElementById("fNamaKategoriBaru").value.trim();
    if (!nama) {
        showKategoriAlert("danger", "Nama kategori wajib diisi.");
        return;
    }

    try {
        const res = await fetch("/api/artikel-kategori", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ nama }),
        });
        if (res.ok) {
            document.getElementById("fNamaKategoriBaru").value = "";
            document.getElementById("kategoriAlert").classList.add("d-none");
            loadKategoriArtikelAdmin();
        } else {
            const data = await res.json();
            showKategoriAlert(
                "danger",
                data.errors?.nama?.[0] ||
                    data.detail ||
                    "Gagal menambah kategori.",
            );
        }
    } catch {
        showKategoriAlert("danger", "Gagal terhubung ke server.");
    }
}

async function renameKategoriArtikel(id, namaLama) {
    const namaBaru = prompt("Ubah nama kategori:", namaLama);
    if (!namaBaru || namaBaru.trim() === "" || namaBaru.trim() === namaLama)
        return;

    try {
        const res = await fetch(`/api/artikel-kategori/${id}`, {
            method: "PUT",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ nama: namaBaru.trim() }),
        });
        if (res.ok) loadKategoriArtikelAdmin();
        else {
            const data = await res.json();
            alert(
                data.errors?.nama?.[0] ||
                    data.detail ||
                    "Gagal mengubah kategori.",
            );
        }
    } catch {
        alert("Gagal terhubung ke server.");
    }
}

async function hapusKategoriArtikel(id, nama) {
    if (!confirm(`Hapus kategori "${nama}"?`)) return;
    try {
        const res = await fetch(`/api/artikel-kategori/${id}`, {
            method: "DELETE",
        });
        if (res.ok) loadKategoriArtikelAdmin();
        else {
            const data = await res.json();
            alert(data.detail || "Gagal menghapus kategori.");
        }
    } catch {
        alert("Gagal terhubung ke server.");
    }
}

async function loadKategoriOptions() {
    const select = document.getElementById("fKategori");
    try {
        const res = await fetch("/api/artikel-kategori/manage");
        const data = await res.json();
        const items = data.items || [];
        select.innerHTML = items.length
            ? items
                  .map(
                      (k) =>
                          `<option value="${k.id}" ${typeof ARTIKEL_EDIT_KATEGORI_ID !== "undefined" && Number(ARTIKEL_EDIT_KATEGORI_ID) === Number(k.id) ? "selected" : ""}>${k.nama}</option>`,
                  )
                  .join("")
            : '<option value="">Belum ada kategori</option>';
    } catch {
        select.innerHTML = '<option value="">Gagal memuat kategori</option>';
    }
}

function toggleVideoUrl() {
    const tipe = document.getElementById("fTipe")?.value;
    document
        .getElementById("wrapVideoUrl")
        ?.classList.toggle("d-none", tipe !== "video");

    const fThumbnail = document.getElementById("fThumbnail");
    if (!fThumbnail) return;

    if (tipe === "video") {
        fThumbnail.readOnly = true;
        fThumbnail.placeholder = "Otomatis terisi dari URL video";
        updateThumbnailFromVideoUrl();
    } else {
        fThumbnail.readOnly = false;
        fThumbnail.placeholder = "https://...";
    }
}

function normalizeThumbnailUrl(url) {
    if (!url) return url;
    let id = null;
    const fileMatch = url.match(
        /drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/,
    );
    const openMatch = url.match(
        /drive\.google\.com\/open\?id=([a-zA-Z0-9_-]+)/,
    );
    const thumbMatch = url.match(
        /drive\.google\.com\/thumbnail\?id=([a-zA-Z0-9_-]+)/,
    );
    if (fileMatch) id = fileMatch[1];
    else if (openMatch) id = openMatch[1];
    else if (thumbMatch) id = thumbMatch[1];
    return id ? `${window.location.origin}/thumbnail-proxy/${id}` : url;
}

function getYoutubeId(url) {
    const match = String(url || "").match(
        /(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{11})/,
    );
    return match ? match[1] : null;
}

function updateThumbnailFromVideoUrl() {
    const videoUrl = document.getElementById("fVideoUrl")?.value?.trim();
    const fThumbnail = document.getElementById("fThumbnail");
    if (!fThumbnail) return;

    const youtubeId = getYoutubeId(videoUrl);
    fThumbnail.value = youtubeId
        ? `https://img.youtube.com/vi/${youtubeId}/hqdefault.jpg`
        : "";
}

async function loadArtikelAdmin() {
    artikelPage = 1;
    await fetchArtikelAdmin();
}

async function fetchArtikelAdmin() {
    const q = document.getElementById("searchArtikel")?.value || "";
    const params = new URLSearchParams({
        limit: 10,
        page: artikelPage,
        ...(q && { q }),
    });

    const tbody = document.getElementById("tabelArtikelAdmin");
    if (tbody)
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4"><div class="spinner-border spinner-border-sm text-success"></div></td></tr>`;

    try {
        const res = await fetch(`/api/artikel/manage?${params}`);
        const data = await res.json();
        renderTabelArtikel(data.items || []);
        renderPaginasiArtikel(data.total || 0, 10);
    } catch {
        if (tbody)
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">Gagal memuat data.</td></tr>`;
    }
}

function renderTabelArtikel(items) {
    const tbody = document.getElementById("tabelArtikelAdmin");
    if (!tbody) return;

    if (!items.length) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">Belum ada artikel.</td></tr>`;
        return;
    }

    tbody.innerHTML = items
        .map(
            (a, i) => `
    <tr>
      <td class="text-muted small">${(artikelPage - 1) * 10 + i + 1}</td>
      <td style="font-weight:600">${a.judul}</td>
      <td><span class="badge rounded-pill ${a.is_active ? "bg-success" : "bg-secondary"}">${a.is_active ? "Aktif" : "Nonaktif"}</span></td>
      <td class="text-muted small">${(a.created_at || "").slice(0, 10)}</td>
      <td>
        <div class="d-flex gap-1">
          <a href="/admin/artikel/edit/${a.id}" class="btn btn-admin-edit btn-sm"><i class="bi bi-pencil"></i></a>
          <button class="btn btn-admin-danger btn-sm" onclick="hapusArtikel(${a.id}, '${a.judul.replace(/'/g, "\\'")}')"><i class="bi bi-trash"></i></button>
        </div>
      </td>
    </tr>`,
        )
        .join("");
}

function renderPaginasiArtikel(total, perPage) {
    const wrap = document.getElementById("paginasiArtikel");
    if (!wrap) return;
    const totalPages = Math.ceil(total / perPage);
    if (totalPages <= 1) {
        wrap.innerHTML = "";
        return;
    }

    wrap.innerHTML = `
    <nav><ul class="pagination admin-pagination mb-0">
      ${Array.from({ length: totalPages }, (_, i) => i + 1)
          .map(
              (p) => `
        <li class="page-item ${p === artikelPage ? "active" : ""}">
          <button class="page-link" onclick="goPageArtikel(${p})">${p}</button>
        </li>`,
          )
          .join("")}
    </ul></nav>`;
}

async function goPageArtikel(page) {
    artikelPage = page;
    await fetchArtikelAdmin();
}

async function hapusArtikel(id, judul) {
    if (!confirm(`Hapus artikel "${judul}"?`)) return;
    try {
        const res = await fetch(`/api/artikel/${id}`, { method: "DELETE" });
        if (res.ok) loadArtikelAdmin();
        else alert("Gagal menghapus artikel.");
    } catch {
        alert("Gagal terhubung ke server.");
    }
}

// =====================
// FORM TAMBAH/EDIT
// =====================
async function submitArtikel(id) {
    const judul = document.getElementById("fJudul")?.value?.trim();
    const konten = document.getElementById("fKonten")?.value?.trim();
    const thumbnail = normalizeThumbnailUrl(
        document.getElementById("fThumbnail")?.value?.trim(),
    );
    const kategoriId = document.getElementById("fKategori")?.value;
    const tipe = document.getElementById("fTipe")?.value;
    const videoUrl = document.getElementById("fVideoUrl")?.value?.trim();
    const isActive = document.getElementById("fIsActive")?.checked;

    if (!judul || !konten) {
        showFormAlert("danger", "Judul dan konten wajib diisi.");
        return;
    }
    if (!kategoriId) {
        showFormAlert("danger", "Kategori wajib dipilih.");
        return;
    }
    if (tipe === "video" && !videoUrl) {
        showFormAlert("danger", "URL video wajib diisi untuk tipe Video.");
        return;
    }

    const payload = {
        judul,
        konten,
        thumbnail: thumbnail || null,
        kategori_id: kategoriId,
        tipe,
        video_url: tipe === "video" ? videoUrl : null,
        is_active: isActive,
    };
    const method = id ? "PUT" : "POST";
    const endpoint = id ? `/api/artikel/${id}` : "/api/artikel";

    try {
        const res = await fetch(endpoint, {
            method,
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (res.ok) {
            window.location.href = "/admin/artikel";
            return;
        }

        let message = data.detail || data.message || "Gagal menyimpan artikel.";
        if (data.errors) {
            const firstError = Object.values(data.errors).flat()[0];
            if (firstError) message = firstError;
        }
        showFormAlert("danger", message);
    } catch {
        showFormAlert("danger", "Gagal terhubung ke server.");
    }
}

function showFormAlert(type, msg) {
    const el = document.getElementById("formAlert");
    if (!el) return;
    el.className = `alert alert-${type}`;
    el.textContent = msg;
    el.classList.remove("d-none");
}
