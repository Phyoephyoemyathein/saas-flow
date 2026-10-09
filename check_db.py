import sqlite3

c = sqlite3.connect(r"c:\laragon\www\saas-flow\database\database.sqlite")
tables = [r[0] for r in c.execute("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")]
print("tables:", tables)
