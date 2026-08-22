import os
import mysql.connector
import pandas as pd

# Let's write a python script to connect to mysql and analyze the contabil_movimento table
try:
    conn = mysql.connector.connect(
        host="127.0.0.1",
        user="root",
        password="",
        database="projetoame"
    )
    cursor = conn.cursor()
except Exception as e:
    print("Error connecting to database:", e)
    exit()

# Fetch all records
query = "SELECT id, data_prevista, data_realizada, descricao, valor_previsto, valor_realizado FROM contabil_movimento ORDER BY data_prevista ASC"
df = pd.read_sql(query, conn)
conn.close()

# Convert dates to datetime
df['data_prevista'] = pd.to_datetime(df['data_prevista'])
df['data_realizada'] = pd.to_datetime(df['data_realizada'])
df['ano_mes'] = df['data_prevista'].dt.to_period('M')

print("Total records:", len(df))
print("Date range:", df['data_prevista'].min(), "to", df['data_prevista'].max())

# Group by month and analyze
monthly = df.groupby('ano_mes').agg(
    count=('id', 'count'),
    entradas=('valor_realizado', lambda x: x[x > 0].sum()),
    saidas=('valor_realizado', lambda x: x[x < 0].sum()),
    saldo=('valor_realizado', 'sum')
).reset_index()

monthly['acumulado'] = monthly['saldo'].cumsum()

print("\n--- Full Monthly Table ---")
print(monthly.to_string())

# Find months with 0 transactions
all_months = pd.period_range(start=df['ano_mes'].min(), end=df['ano_mes'].max(), freq='M')
missing_months = [m for m in all_months if m not in monthly['ano_mes'].values]
print("\n--- Missing Months (0 transactions) ---")
print(missing_months)

# Find months with NO outflows (saidas)
no_outflows = monthly[monthly['saidas'] == 0]
print("\n--- Months with NO Outflows ---")
print(no_outflows.to_string())

# Find months with very low outflows (e.g. less than 10% of inflows or < R$ 100) when inflows are high
suspicious = []
for idx, row in monthly.iterrows():
    ent = row['entradas']
    sai = abs(row['saidas'])
    if ent > 1000 and (sai < 100 or sai < 0.05 * ent):
        suspicious.append(row)
print("\n--- Suspicious Months (High Inflows, Extremely Low Outflows) ---")
if suspicious:
    print(pd.DataFrame(suspicious).to_string())
else:
    print("None found based on simple threshold.")
