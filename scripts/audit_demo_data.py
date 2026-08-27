import sqlite3
from pathlib import Path

path = Path(__file__).resolve().parents[1] / "database" / "database.sqlite"
connection = sqlite3.connect(path)
for table in ("teams", "users", "companies", "contacts", "leads", "deals", "tasks", "activities", "notes", "pipelines", "stages"):
    try:
        count = connection.execute(f"select count(*) from {table}").fetchone()[0]
    except sqlite3.Error as error:
        count = f"ERROR: {error}"
    print(f"{table}: {count}")
