# Service untuk memproses file PDF modul
# Parse PDF → potong jadi chunks → siap diindex ke ChromaDB

def baca_pdf(path_file: str) -> list[dict]:
    """
    Baca PDF dan kembalikan list halaman.
    Return: [{'halaman': 1, 'teks': '...'}, ...]
    """
    try:
        import fitz  # PyMuPDF
    except ImportError:
        raise RuntimeError("PyMuPDF belum diinstall. Jalankan: pip install pymupdf")

    dokumen = fitz.open(path_file)
    halaman_list = []

    for i, halaman in enumerate(dokumen):
        teks = halaman.get_text().strip()
        if teks:  # Abaikan halaman kosong
            halaman_list.append({'halaman': i + 1, 'teks': teks})

    dokumen.close()
    return halaman_list


def potong_jadi_chunks(halaman_list: list[dict], ukuran: int = 400) -> list[dict]:
    """
    Potong teks per halaman menjadi chunks ~400 kata.
    Setiap chunk menyimpan metadata nomor halaman.
    """
    semua_chunks = []

    for halaman in halaman_list:
        kata_kata = halaman['teks'].split()

        for i in range(0, len(kata_kata), ukuran):
            chunk_teks = ' '.join(kata_kata[i:i + ukuran])
            if chunk_teks.strip():
                semua_chunks.append({
                    'teks': chunk_teks,
                    'halaman': halaman['halaman'],
                })

    return semua_chunks
