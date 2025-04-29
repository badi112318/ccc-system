import matplotlib.pyplot as plt
import mysql.connector

# Connect to the database
conn = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",  # Update as needed
    database="cctv_db"
)

cursor = conn.cursor()
cursor.execute("SELECT agency, COUNT(*) AS count FROM footages GROUP BY agency ORDER BY count DESC")
data = cursor.fetchall()

agencies = [row[0] for row in data]
counts = [row[1] for row in data]

# Plot using matplotlib
plt.figure(figsize=(6, 6))
plt.pie(counts, labels=agencies, autopct='%1.1f%%', startangle=140)
plt.title("Agency Distribution")
plt.axis('equal')

# Save the chart as a PNG image
plt.savefig("generated/agency_chart.png", bbox_inches='tight')
plt.close()

cursor.close()
conn.close()
