import tkinter as tk
from tkinter import ttk, messagebox, simpledialog
import mysql.connector
import csv
import logging
import os

# ─── Logging ────────────────────────────────────────────
LOG_FILE = os.path.join(os.path.dirname(os.path.abspath(__file__)), "gestion_stock.log")

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    datefmt="%Y-%m-%d %H:%M:%S",
    handlers=[
        logging.FileHandler(LOG_FILE, encoding="utf-8"),
        logging.StreamHandler(),
    ],
)
logger = logging.getLogger(__name__)

# ─── Connexion à la base de données ────────────────────
DB = {
    "host":     "localhost",
    "database": "gestion_stock",
    "user":     "stock_user",
    "password": "motdepasse"
}

def connexion():
    try:
        con = mysql.connector.connect(**DB)
        return con
    except mysql.connector.Error as e:
        logger.error("Échec de connexion à la base de données : %s", e)
        raise

# ─── Fonctions base de données ──────────────────────────

def get_produits():
    con = connexion()
    cur = con.cursor()
    cur.execute("SELECT id, nom, quantite, prix FROM produits ORDER BY nom")
    rows = cur.fetchall()
    con.close()
    return rows

def ajouter_en_base(nom, quantite, prix):
    con = connexion()
    cur = con.cursor()
    cur.execute("INSERT INTO produits (nom, quantite, prix) VALUES (%s, %s, %s)", (nom, quantite, prix))
    con.commit()
    con.close()
    logger.info("Produit ajouté : nom='%s', quantité=%d, prix=%.2f€", nom, quantite, prix)
    if quantite < SEUIL_ALERTE:
        logger.warning("Stock faible à l'ajout : '%s' (quantité=%d < seuil=%d)", nom, quantite, SEUIL_ALERTE)

def modifier_en_base(produit_id, nom, quantite, prix):
    con = connexion()
    cur = con.cursor()
    cur.execute("UPDATE produits SET nom = %s, quantite = %s, prix = %s WHERE id = %s", (nom, quantite, prix, produit_id))
    con.commit()
    con.close()
    logger.info("Produit modifié : id=%d, nom='%s', quantité=%d, prix=%.2f€", produit_id, nom, quantite, prix)
    if quantite < SEUIL_ALERTE:
        logger.warning("Stock faible après modification : '%s' (id=%d, quantité=%d < seuil=%d)", nom, produit_id, quantite, SEUIL_ALERTE)

def supprimer_en_base(produit_id, nom=""):
    con = connexion()
    cur = con.cursor()
    cur.execute("DELETE FROM produits WHERE id = %s", (produit_id,))
    con.commit()
    con.close()
    logger.info("Produit supprimé : id=%d, nom='%s'", produit_id, nom)

# ─── Fonctions interface ────────────────────────────────

SEUIL_ALERTE = 5

def actualiser_tableau():
    tableau.delete(*tableau.get_children())
    for row in get_produits():
        produit_id, nom, quantite, prix = row
        alerte = " ⚠ STOCK FAIBLE" if quantite < SEUIL_ALERTE else ""
        tableau.insert("", "end", iid=produit_id, values=(nom, quantite, f"{prix:.2f} €" + alerte))

def ajouter_produit():
    nom = simpledialog.askstring("Ajouter", "Nom du produit :")
    if not nom: return
    quantite = simpledialog.askinteger("Ajouter", "Quantité :")
    if quantite is None: return
    prix = simpledialog.askfloat("Ajouter", "Prix unitaire (€) :")
    if prix is None: return

    ajouter_en_base(nom, quantite, prix)
    actualiser_tableau()

def modifier_quantite():
    selection = tableau.selection()
    if not selection:
        messagebox.showwarning("Attention", "Sélectionnez un produit.")
        return
    produit_id = int(selection[0])
    valeurs = tableau.item(produit_id)["values"]
    ancien_nom = valeurs[0]

    nouveau_nom = simpledialog.askstring("Modifier", "Nom du produit :", initialvalue=ancien_nom)
    if nouveau_nom is None: return
    nouvelle_qte = simpledialog.askinteger("Modifier", "Quantité :", initialvalue=valeurs[1])
    if nouvelle_qte is None: return
    nouveau_prix = simpledialog.askfloat("Modifier", "Prix unitaire (€) :", initialvalue=float(str(valeurs[2]).replace(" €", "").replace(" ⚠ STOCK FAIBLE", "")))
    if nouveau_prix is None: return

    modifier_en_base(produit_id, nouveau_nom, nouvelle_qte, nouveau_prix)
    actualiser_tableau()

def supprimer_produit():
    selection = tableau.selection()
    if not selection:
        messagebox.showwarning("Attention", "Sélectionnez un produit.")
        return
    produit_id = int(selection[0])
    nom = tableau.item(produit_id)["values"][0]
    if messagebox.askyesno("Supprimer", f"Supprimer '{nom}' ?"):
        supprimer_en_base(produit_id, nom)
        actualiser_tableau()

def exporter_csv():
    produits = get_produits()
    with open("export_stock.csv", "w", newline="", encoding="utf-8") as f:
        writer = csv.writer(f)
        writer.writerow(["Nom", "Quantite", "Prix"])
        for _, nom, quantite, prix in produits:
            writer.writerow([nom, quantite, f"{prix:.2f}"])
    logger.info("Export CSV effectué : %d produit(s) exporté(s) dans 'export_stock.csv'", len(produits))
    messagebox.showinfo("Export", "Stock exporté dans 'export_stock.csv'")

def voir_historique():
    con = connexion()
    cur = con.cursor()
    cur.execute("""
        SELECT h.ancien_nom, h.nouveau_nom,
               h.ancien_stock, h.nouveau_stock,
               h.ancien_prix, h.nouveau_prix,
               h.date_modif
        FROM historique h
        ORDER BY h.date_modif DESC
        LIMIT 20
    """)
    rows = cur.fetchall()
    con.close()

    fenetre_hist = tk.Toplevel(fenetre)
    fenetre_hist.title("Historique des modifications")
    fenetre_hist.geometry("820x300")

    cols = ("Ancien nom", "Nouveau nom", "Anc. stock", "Nouv. stock", "Anc. prix", "Nouv. prix", "Date")
    table_hist = ttk.Treeview(fenetre_hist, columns=cols, show="headings")
    largeurs = [120, 120, 80, 80, 80, 80, 150]
    for col, larg in zip(cols, largeurs):
        table_hist.heading(col, text=col)
        table_hist.column(col, width=larg)
    table_hist.pack(fill="both", expand=True, padx=10, pady=10)

    for row in rows:
        ancien_nom, nouveau_nom, anc_stock, nouv_stock, anc_prix, nouv_prix, date = row
        table_hist.insert("", "end", values=(
            ancien_nom, nouveau_nom,
            anc_stock, nouv_stock,
            f"{anc_prix:.2f} €", f"{nouv_prix:.2f} €",
            date
        ))

# ─── Fenêtre principale ─────────────────────────────────

logger.info("Démarrage de l'application Gestion de Stock")
fenetre = tk.Tk()
fenetre.title("Gestion de Stock")
fenetre.geometry("650x430")
fenetre.resizable(False, False)

tk.Label(fenetre, text="Gestion de Stock", font=("Arial", 16, "bold")).pack(pady=10)

colonnes = ("Nom", "Quantité", "Prix")
tableau = ttk.Treeview(fenetre, columns=colonnes, show="headings", height=12)
for col in colonnes:
    tableau.heading(col, text=col)
    tableau.column(col, width=200)
tableau.pack(padx=20)

cadre = tk.Frame(fenetre)
cadre.pack(pady=10)

tk.Button(cadre, text="Ajouter",      width=16, command=ajouter_produit).grid(row=0, column=0, padx=6, pady=3)
tk.Button(cadre, text="Modifier",     width=16, command=modifier_quantite).grid(row=0, column=1, padx=6, pady=3)
tk.Button(cadre, text="Supprimer",    width=16, command=supprimer_produit).grid(row=0, column=2, padx=6, pady=3)
tk.Button(cadre, text="Exporter CSV", width=16, command=exporter_csv).grid(row=1, column=0, padx=6, pady=3)
tk.Button(cadre, text="Historique",   width=16, command=voir_historique).grid(row=1, column=1, padx=6, pady=3)

actualiser_tableau()
fenetre.mainloop()
logger.info("Fermeture de l'application Gestion de Stock")
