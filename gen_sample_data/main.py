from random import seed, uniform, random
import math
from os import system, name 

db_patients_size = 50;
db_inscompany_size = 4;
db_insplans_size = 20;
db_services_size = 150; #or less
db_coverage_size = 150;
db_pharmacies_size = 4;
db_serviceproviders_size = 61;
db_medicalproducts_size = 116;

#Output Data
Bulk_Database = open("xout_bulkdata.txt", "w")

Patients = open("xout_patients.txt", "w")
InsPlans = open("xout_insplans.txt", "w")
InsCompany = open("xout_inscompany.txt", "w")
Coverages = open("xout_coverage.txt", "w")
Services = open("xout_services.txt", "w")
ServiceCosts = open("xout_servicecosts.txt", "w")
Membership = open("xout_membership.txt", "w")
ProductTransactions = open("xout_producttransactions.txt", "w")
IncludedService = open("xout_includedservice.txt", "w")
IncludedProducts = open("xout_includedproducts.txt", "w")
ServiceProviders = open("xout_serviceproviders.txt", "w")
ServiceRecords = open("xout_servicerecords.txt", "w")
ServiceProducts = open("xout_serviceproducts.txt", "w")
MedicalProducts = open("xout_medicalproducts.txt", "w")
ProductCosts = open("xout_productcosts.txt", "w")
Pharmacies = open("xout_pharmacies.txt", "w")

#Random Data
name_database = open("names.txt", "r")
address_database = open("addresses.txt", "r")
email_database = open("emails.txt", "r")
employer_database = open("employers.txt", "r")
company_database = open("companies.txt", "r")
treatment_database = open("treatment.txt", "r")
services_database = open("services.txt", "r")
pharmacies_database = open("pharmacies.txt", "r")
institutions_database = open("institutions.txt", "r")
specialty_database = open("specialty.txt", "r")
products_database = open("products.txt", "r")
manufacturers_database = open("manufacturers.txt", "r")
namelines = 0;
addresslines = 0;
emaillines = 0;
employerlines = 0;
companylines = 0;
treatmentlines = 0;
pharmacylines = 0;
institutionlines = 0;
specialtylines = 0;
productslines = 0;
manufacturerslines = 0;

#Line counting
for line in name_database:
  line = line.strip("\n")
  namelines += 1
for line in address_database:
  line = line.strip("\n")
  addresslines += 1
for line in email_database:
  line = line.strip("\n")
  emaillines += 1
for line in employer_database:
  line = line.strip("\n")
  employerlines += 1
for line in company_database:
  line = line.strip("\n")
  companylines += 1
for line in treatment_database:
  line = line.strip("\n")
  treatmentlines += 1
for line in pharmacies_database:
  line = line.strip("\n")
  pharmacylines += 1
for line in institutions_database:
  line = line.strip("\n")
  institutionlines += 1
for line in specialty_database:
  line = line.strip("\n")
  specialtylines += 1
for line in products_database:
  line = line.strip("\n")
  productslines += 1
for line in manufacturers_database:
  line = line.strip("\n")
  manufacturerslines += 1

########################################################################

Patients.write("DELETE FROM Patients;\n")
Patients.write("ALTER TABLE Patients AUTO_INCREMENT = 10000000;\n")
Patients.write("INSERT INTO Patients (SSN, Password, Name, Address, DoB, Phone, Email) VALUES\n")
Bulk_Database.write("DELETE FROM Patients;\n")
Bulk_Database.write("ALTER TABLE Patients AUTO_INCREMENT = 10000000;\n")
Bulk_Database.write("INSERT INTO Patients (SSN, Password, Name, Address, DoB, Phone, Email) VALUES\n")

#Patients
for x in range(db_patients_size):

  #Date of Birth
	dob_year = round(random()*43) + 1960
	dob_month = round(random()*11) + 1
	dob_day = round(random()*27) + 1

  #Address
	addr = round(addresslines*random())

	address_database  = open("addresses.txt", "r")

	for i, line in enumerate(address_database):
		if i == addr:
			address = line

  #Email
	mail = round(emaillines*random())

	email_database  = open("emails.txt", "r")

	for i, line in enumerate(email_database):
		if i == mail:
			email = line

  #Phone Number
	phone = 2080000000 + round(random()*9999999)

  #Name
	firstname = round(namelines*random())
	lastname = round(namelines*random())
	name_database  = open("names.txt", "r")

	for i, line in enumerate(name_database):
		if i == firstname:
			first = line
		if i == lastname:
			last = line

	#Social Security Number
	SSN = 100000000 + round(random()*899999999)

	#String conversion
	first = first.rstrip()
	last = last.rstrip()
	address = address.rstrip()
	email = email.rstrip()

	if (dob_month < 10):
		dob_month = "0" + str(dob_month)

	if (dob_day < 10):
		dob_day = "0" + str(dob_day)

	#Data
	Patients.write("('{}','5f4dcc3b5aa765d61d8327deb882cf99','{} {}','{}','{}{}{}','{}','{}')".format(SSN, first, last, address, dob_year, dob_month, dob_day, phone, email))
	if (x != db_patients_size - 1):
		Patients.write(",\n")
	Bulk_Database.write("('{}','5f4dcc3b5aa765d61d8327deb882cf99','{} {}','{}','{}{}{}','{}','{}')".format(SSN, first, last, address, dob_year, dob_month, dob_day, phone, email))
	if (x != db_patients_size - 1):
		Bulk_Database.write(",\n")

Patients.write(";")
Bulk_Database.write(";\n")

########################################################################

ServiceProviders.write("DELETE FROM ServiceProviders;\n")
ServiceProviders.write("ALTER TABLE ServiceProviders AUTO_INCREMENT = 10000000;\n")
ServiceProviders.write("INSERT INTO ServiceProviders (Password, Name, Institution, Address, Specialty, Phone, Email) VALUES\n")
Bulk_Database.write("DELETE FROM ServiceProviders;\n")
Bulk_Database.write("ALTER TABLE ServiceProviders AUTO_INCREMENT = 10000000;\n")
Bulk_Database.write("INSERT INTO ServiceProviders (Password, Name, Institution, Address, Specialty, Phone, Email) VALUES\n")

spec = 0;

#ServiceProviders
for x in range(db_serviceproviders_size):

  #Address
	addr = round(addresslines*random())

	address_database  = open("addresses.txt", "r")

	for i, line in enumerate(address_database):
		if i == addr:
			address = line

  #Email
	mail = round(emaillines*random())

	email_database  = open("emails.txt", "r")

	for i, line in enumerate(email_database):
		if i == mail:
			email = line

  #Phone Number
	phone = 2080000000 + round(random()*9999999) #Not Exclusive Yet!

  #Name
	firstname = round(namelines*random())
	lastname = round(namelines*random())
	name_database = open("names.txt", "r")

	for i, line in enumerate(name_database):
		if i == firstname:
			first = line
		if i == lastname:
			last = line

	#Institution
	inst = round(institutionlines*random())

	institutions_database = open("institutions.txt", "r")

	for i, line in enumerate(institutions_database):
		if i == inst:
			Institution = line

	#Specialty
	specialty_database = open("specialty.txt", "r")

	for i, line in enumerate(specialty_database):
		if i == spec:
			Specialty = line

	spec += 1;

	#String conversion
	first = first.rstrip()
	last = last.rstrip()
	address = address.rstrip()
	email = email.rstrip()
	Institution = Institution.rstrip()
	Specialty = Specialty.rstrip()

	#Data
	ServiceProviders.write("('5f4dcc3b5aa765d61d8327deb882cf99','{} {}','{}','{}','{}','{}','{}')".format(first, last, Institution, address, Specialty, phone, email))
	if (x != db_serviceproviders_size - 1):
		ServiceProviders.write(",\n")
	Bulk_Database.write("('5f4dcc3b5aa765d61d8327deb882cf99','{} {}','{}','{}','{}','{}','{}')".format(first, last, Institution, address, Specialty, phone, email))
	if (x != db_serviceproviders_size - 1):
		Bulk_Database.write(",\n")

ServiceProviders.write(";")
Bulk_Database.write(";\n")

########################################################################

IncludedService.write("DELETE FROM IncludedService;\n")
IncludedService.write("ALTER TABLE IncludedService AUTO_INCREMENT = 10000000;\n")
IncludedService.write("INSERT INTO IncludedService (ServiceCode, Specialty) VALUES\n")
Bulk_Database.write("DELETE FROM IncludedService;\n")
Bulk_Database.write("ALTER TABLE IncludedService AUTO_INCREMENT = 10000000;\n")
Bulk_Database.write("INSERT INTO IncludedService (ServiceCode, Specialty) VALUES\n")

ServiceCode = 10000000 - 1;
spec = -1;

#IncludedService
for x in range(db_services_size):

	#Specialty
	specialty_database = open("specialty.txt", "r")

	for i, line in enumerate(specialty_database):
		if i == spec:
			Specialty = line

	spec += 1

	if (spec > specialtylines):
		spec = 0

	#ServiceCode
	ServiceCode += 1;

	#String conversion
	Specialty = Specialty.rstrip()

	#Data
	IncludedService.write("('{}','{}')".format(ServiceCode, Specialty))
	if (x != db_services_size - 1):
		IncludedService.write(",\n")
	Bulk_Database.write("('{}',{}')".format(ServiceCode, Specialty))
	if (x != db_services_size - 1):
		Bulk_Database.write(",\n")

IncludedService.write(";")
Bulk_Database.write(";\n")

########################################################################

ServiceProducts.write("DELETE FROM ServiceProducts;\n")
ServiceProducts.write("INSERT INTO ServiceProducts (ServiceCode, ProdID) VALUES\n")
Bulk_Database.write("DELETE FROM ServiceProducts;\n")
Bulk_Database.write("INSERT INTO ServiceProducts (ServiceCode, ProdID) VALUES\n")

ServiceCode = 10000000 - 1;

#IncludedService
for x in range(db_services_size):

	#ServiceCode
	ServiceCode += 1;

	rand_range = round(random()*5) - 1

	for x in range(rand_range):
		#Product ID
		ProdID = 10000000 + round(random()*productslines) - 1;

		#Data
		ServiceProducts.write("('{}','{}')".format(ServiceCode, ProdID))
		if (x != db_services_size - 1):
			ServiceProducts.write(",\n")
		Bulk_Database.write("('{}',{}')".format(ServiceCode, ProdID))
		if (x != db_services_size - 1):
			Bulk_Database.write(",\n")

ServiceProducts.write(";")
Bulk_Database.write(";\n")

########################################################################

Membership.write("DELETE FROM Membership;\n")
Membership.write("ALTER TABLE Membership AUTO_INCREMENT = 10000000;\n")
Membership.write("INSERT INTO Membership (PID, PlanID) VALUES\n")
Bulk_Database.write("DELETE FROM Membership;\n")
Bulk_Database.write("ALTER TABLE Membership AUTO_INCREMENT = 10000000;\n")
Bulk_Database.write("INSERT INTO Membership (PID, PlanID) VALUES\n")

PID = 10000000 - 1;

#IncludedService
for x in range(db_patients_size):

	#Patient ID
	PID += 1;

	#Plan ID
	PlanID = 10000000 + round(random()*(db_insplans_size - 1))

	#Data
	Membership.write("('{}','{}')".format(PID, PlanID))
	if (x != db_patients_size - 1):
		Membership.write(",\n")
	Bulk_Database.write("('{}',{}')".format(PID, PlanID))
	if (x != db_patients_size - 1):
		Bulk_Database.write(",\n")

Membership.write(";")
Bulk_Database.write(";\n")

########################################################################

ProductTransactions.write("DELETE FROM ProductTransactions;\n")
ProductTransactions.write("ALTER TABLE ProductTransactions AUTO_INCREMENT = 10000000;\n")
ProductTransactions.write("INSERT INTO ProductTransactions (PID, ProdID, Date) VALUES\n")
Bulk_Database.write("DELETE FROM ProductTransactions;\n")
Bulk_Database.write("ALTER TABLE ProductTransactions AUTO_INCREMENT = 10000000;\n")
Bulk_Database.write("INSERT INTO ProductTransactions (PID, ProdID, Date) VALUES\n")

PID = 10000000 - 1;

#IncludedService
for x in range(db_patients_size):

	#Patient ID
	PID += 1;

	rand_range = round(random()*5) - 1

	for x in range(rand_range):

		#Plan ID
		ProdID = 10000000 + round(random()*(db_medicalproducts_size - 1))

		#Date
		dob_year = 2020#round(random()*43) + 1960
		dob_month = round(random()*11) + 1
		dob_day = round(random()*27) + 1

		if (dob_month < 10):
			dob_month = "0" + str(dob_month)

		if (dob_day < 10):
			dob_day = "0" + str(dob_day)

		#Data
		ProductTransactions.write("('{}','{}','{}{}{}')".format(PID, ProdID, dob_year, dob_month, dob_day))
		if (x != db_patients_size - 1):
			ProductTransactions.write(",\n")
		Bulk_Database.write("('{}',{}','{}{}{}')".format(PID, ProdID, dob_year, dob_month, dob_day))
		if (x != db_patients_size - 1):
			Bulk_Database.write(",\n")

ProductTransactions.write(";")
Bulk_Database.write(";\n")

########################################################################

ServiceRecords.write("DELETE FROM ServiceRecords;\n")
ServiceRecords.write("ALTER TABLE ServiceRecords AUTO_INCREMENT = 10000000;\n")
ServiceRecords.write("INSERT INTO ServiceRecords (PID, ProvID, ServiceCode, Date, Hour, Minute) VALUES\n")
Bulk_Database.write("DELETE FROM ServiceRecords;\n")
Bulk_Database.write("ALTER TABLE ServiceRecords AUTO_INCREMENT = 10000000;\n")
Bulk_Database.write("INSERT INTO ServiceRecords (PID, ProvID, ServiceCode, Date, Hour, Minute) VALUES\n")

PID = 10000000 - 1;

#IncludedService
for x in range(db_patients_size):

	#Patient ID
	PID += 1;

	rand_range = round(random()*8) + 3

	for x in range(rand_range):

		#Prov ID
		ProvID = 10000000 + round(random()*(db_serviceproviders_size - 1))

		#Service Code
		ServiceCode = 10000000 + round(random()*(db_services_size - 1))

		#Date
		year = 2020#round(random()*43) + 1960
		month = round(random()*11) + 1
		day = round(random()*27) + 1

		if (month < 10):
			month = "0" + str(month)

		if (day < 10):
			day = "0" + str(day)

		#Hour
		hour = round(random()*7) + 9

		#Minute
		minute = "00"

		#Data
		ServiceRecords.write("('{}','{}','{}','{}{}{}','{}','{}')".format(PID, ProvID, ServiceCode, year, month, day, hour, minute))
		if (x != db_patients_size - 1):
			ServiceRecords.write(",\n")
		Bulk_Database.write("('{}',{}','{}','{}{}{}','{}','{}')".format(PID, ProvID, ServiceCode, year, month, day, hour, minute))
		if (x != db_patients_size - 1):
			Bulk_Database.write(",\n")

ServiceRecords.write(";")
Bulk_Database.write(";\n")

########################################################################

InsCompany.write("DELETE FROM InsCompany;\n")
InsCompany.write("ALTER TABLE InsCompany AUTO_INCREMENT = 10000000;\n")
InsCompany.write("INSERT INTO InsCompany (Email, Password, Name, Address, Acronym) VALUES\n")
Bulk_Database.write("DELETE FROM InsCompany;\n")
Bulk_Database.write("ALTER TABLE InsCompany AUTO_INCREMENT = 10000000;\n")
Bulk_Database.write("INSERT INTO InsCompany (Email, Password, Name, Address, Acronym) VALUES\n")

comp = round(companylines*random())

while (comp > (companylines - db_inscompany_size)):
	comp = round(companylines*random())

#InsCompany
for x in range(db_inscompany_size):

	#Email
	mail = round(emaillines*random())

	email_database  = open("emails.txt", "r")

	for i, line in enumerate(email_database):
		if i == mail:
			email = line

	#Company
	company_database = open("companies.txt","r")

	for i, line in enumerate(company_database):
		if i == comp:
			company = line

	comp += 1

	#Address
	addr = round(addresslines*random())

	address_database  = open("addresses.txt", "r")

	for i, line in enumerate(address_database):
		if i == addr:
			address = line

	#Acronym
	acronym = company[0:2].upper();
	
	#String conversion
	company = company.rstrip()
	address = address.rstrip()
	email = email.rstrip()

	#Data
	InsCompany.write("('{}','5f4dcc3b5aa765d61d8327deb882cf99','{}','{}','{}')".format(email, company, address, acronym))
	if (x != db_inscompany_size - 1):
		InsCompany.write(",\n")
	Bulk_Database.write("('{}','5f4dcc3b5aa765d61d8327deb882cf99','{}','{}','{}')".format(email, company, address, acronym))
	if (x != db_inscompany_size - 1):
		Bulk_Database.write(",\n")

InsCompany.write(";")
Bulk_Database.write(";\n")

########################################################################

Pharmacies.write("DELETE FROM Pharmacies;\n")
Pharmacies.write("ALTER TABLE Pharmacies AUTO_INCREMENT = 10000000;\n")
Pharmacies.write("INSERT INTO Pharmacies (Email, Password, Name, Address) VALUES\n")
Bulk_Database.write("DELETE FROM Pharmacies;\n")
Bulk_Database.write("ALTER TABLE Pharmacies AUTO_INCREMENT = 10000000;\n")
Bulk_Database.write("INSERT INTO Pharmacies (Email, Password, Name, Address) VALUES\n")

pharm = round(pharmacylines*random())

while (pharm > (pharmacylines - db_pharmacies_size)):
	pharm = round(pharmacylines*random())

pharm_prod = pharm;

#Pharmacies
for x in range(db_pharmacies_size):

	#Email
	mail = round(emaillines*random())

	email_database  = open("emails.txt", "r")

	for i, line in enumerate(email_database):
		if i == mail:
			email = line

	#Pharmacy
	pharmacies_database = open("pharmacies.txt", "r")

	for i, line in enumerate(pharmacies_database):
		if i == pharm:
			Name = line

	pharm += 1

	#Address
	addr = round(addresslines*random())

	address_database = open("addresses.txt", "r")

	for i, line in enumerate(address_database):
		if i == addr:
			Address = line

	#String conversion
	Name = Name.rstrip()
	Address = Address.rstrip()
	email = email.rstrip()

	#Data
	Pharmacies.write("('{}','5f4dcc3b5aa765d61d8327deb882cf99','{}','{}')".format(email, Name, Address))
	if (x != db_pharmacies_size - 1):
		Pharmacies.write(",\n")
	Bulk_Database.write("('{}','5f4dcc3b5aa765d61d8327deb882cf99','{}','{}')".format(email, Name, Address))
	if (x != db_pharmacies_size - 1):
		Bulk_Database.write(",\n")

Pharmacies.write(";")
Bulk_Database.write(";\n")

########################################################################

MedicalProducts.write("DELETE FROM MedicalProducts;\n")
MedicalProducts.write("ALTER TABLE MedicalProducts AUTO_INCREMENT = 10000000;\n")
MedicalProducts.write("INSERT INTO MedicalProducts (Description, Manufacturer) VALUES\n")
Bulk_Database.write("DELETE FROM MedicalProducts;\n")
Bulk_Database.write("ALTER TABLE MedicalProducts AUTO_INCREMENT = 10000000;\n")
Bulk_Database.write("INSERT INTO MedicalProducts (Description, Manufacturer) VALUES\n")

prod = 0;

#MedicalProducts/ProductCosts
for x in range(db_medicalproducts_size):

	#Product Description
	#prod = round(random()*productslines)

	products_database = open("products.txt", "r")

	for i, line in enumerate(products_database):
		if i == prod:
			Description = line

	prod += 1

	#Manufacturer
	man = round(random()*manufacturerslines)

	manufacturers_database = open("manufacturers.txt", "r")

	for i, line in enumerate(manufacturers_database):
		if i == man:
			Manufacturer = line

	#String conversion
	Name = Name.rstrip()
	Manufacturer = Manufacturer.rstrip()

	#String conversion
	Description = Description.rstrip()
	Manufacturer = Manufacturer.rstrip()

	#Data
	MedicalProducts.write("('{}','{}')".format(Description, Manufacturer))
	if (x != db_medicalproducts_size - 1):
		MedicalProducts.write(",\n")
	Bulk_Database.write("('{}','{}')".format(Description, Manufacturer))
	if (x != db_medicalproducts_size - 1):
		Bulk_Database.write(",\n")

MedicalProducts.write(";")
Bulk_Database.write(";\n")

########################################################################

IncludedProducts.write("DELETE FROM IncludedProducts;\n")
IncludedProducts.write("INSERT INTO IncludedProducts (PlanID, ProdID, Discount) VALUES\n")
Bulk_Database.write("DELETE FROM IncludedProducts;\n")
Bulk_Database.write("INSERT INTO IncludedProducts (PlanID, ProdID, Discount) VALUES\n")

ProdID = 10000000

#IncludedProducts/ProductCosts
for x in range(db_medicalproducts_size):

	#Plan ID
	PlanID = 10000000 + round(random()*(db_insplans_size - 1))

	#Product ID
	ProdID += 1

	#Discount
	Discount = round(random()*50)

	#Data
	IncludedProducts.write("('{}','{}','{}')".format(PlanID, ProdID, Discount))
	if (x != db_medicalproducts_size - 1):
		IncludedProducts.write(",\n")
	Bulk_Database.write("('{}','{}','{}')".format(Description, ProdID, Discount))
	if (x != db_medicalproducts_size - 1):
		Bulk_Database.write(",\n")

IncludedProducts.write(";")
Bulk_Database.write(";\n")

########################################################################

ProductCosts.write("DELETE FROM ProductCosts;\n")
ProductCosts.write("INSERT INTO ProductCosts (ProdID, PharmID, Amount, Cost) VALUES\n")
Bulk_Database.write("DELETE FROM ProductCosts;\n")
Bulk_Database.write("INSERT INTO ProductCosts (ProdID, PharmID, Amount, Cost) VALUES\n")

ProdID = 10000000;
ProdID -= 1;

#ProductCosts
for x in range(db_medicalproducts_size):

	#Product ID
	ProdID += 1

	#Pharmacy ID
	PharmID = 10000000 + round(random()*(db_pharmacies_size - 1))

	#Cost
	Cost = round(random()*800)

	#Amount
	Amount = round(random()*10000)
	Amount *= pow(10, 1)

	#Data
	ProductCosts.write("('{}','{}','{}','{}')".format(ProdID, PharmID, Amount, Cost))
	if (x != db_medicalproducts_size - 1):
		ProductCosts.write(",\n")
	Bulk_Database.write("('{}','{}','{}','{}')".format(ProdID, PharmID, Amount, Cost))
	if (x != db_medicalproducts_size - 1):
		Bulk_Database.write(",\n")

ProductCosts.write(";")
Bulk_Database.write(";\n")

########################################################################

InsPlans.write("DELETE FROM InsPlans;\n")
InsPlans.write("ALTER TABLE InsPlans AUTO_INCREMENT = 10000000;\n")
InsPlans.write("INSERT INTO InsPlans (CompID, AnnualPremium, AnnualDeductible, PlanContribution, MaxCoverage) VALUES\n")
Bulk_Database.write("DELETE FROM InsPlans;\n")
Bulk_Database.write("ALTER TABLE InsPlans AUTO_INCREMENT = 10000000;\n")
Bulk_Database.write("INSERT INTO InsPlans (CompID, AnnualPremium, AnnualDeductible, PlanContribution, MaxCoverage) VALUES\n")

#InsPlans
for x in range(db_insplans_size):

	#Company ID
	CompID = 10000000 + round(random()*(db_inscompany_size - 1))

	#AnnualPrem
	AnnualPrem = 1000 + round(random()*10000)

	#AnnualPrem
	AnnualDeductible = 1000 + round(random()*9000)

	#PlanContribution
	PlanContribution = 1000 + round(random()*4000)

	#MaxCoverage
	MaxCoverage = 5000 + round(random()*10000)

	#Data
	InsPlans.write("('{}','{}','{}','{}','{}')".format(CompID, AnnualPrem, AnnualDeductible, PlanContribution, MaxCoverage))
	if (x != db_insplans_size - 1):
		InsPlans.write(",\n")
	Bulk_Database.write("('{}','{}','{}','{}','{}')".format(CompID, AnnualPrem, AnnualDeductible, PlanContribution, MaxCoverage))
	if (x != db_insplans_size - 1):
		Bulk_Database.write(",\n")

InsPlans.write(";")
Bulk_Database.write(";\n")

########################################################################

Services.write("DELETE FROM Services;\n")
Services.write("ALTER TABLE Services AUTO_INCREMENT = 10000000;\n")
Services.write("INSERT INTO Services (Description, ApprovedTherapy, Cost) VALUES\n")
Bulk_Database.write("DELETE FROM Services;\n")
Bulk_Database.write("ALTER TABLE Services AUTO_INCREMENT = 10000000;\n")
Bulk_Database.write("INSERT INTO Services (Description, ApprovedTherapy, Cost) VALUES\n")

serv = 0

#Services
for x in range(db_services_size):
	
	#ServiceType
	services_database = open("services.txt","r")

	for i, line in enumerate(services_database):
		if i == serv:
			Description = line

	serv += 1

	#Approved Therapy
	approval = round(random()*2)
	
	if (approval == 1):
		ApprovedTherapy = "Yes"
	elif (approval == 2):
		ApprovedTherapy = "No"
	else:
		ApprovedTherapy = "Yes"
	
	#String conversion
	Description = Description.rstrip()

	#Cost
	Cost = round(random()*3000)

	#Data
	Services.write("('{}','{}','{}')".format(Description, ApprovedTherapy, Cost))
	if (x != db_services_size - 1):
		Services.write(",\n")
	Bulk_Database.write("('{}','{}','{}')".format(Description, ApprovedTherapy, Cost))
	if (x != db_services_size - 1):
		Bulk_Database.write(",\n")

Services.write(";")
Bulk_Database.write(";\n")

########################################################################

Coverages.write("DELETE FROM Coverages;\n")
Coverages.write("ALTER TABLE Coverages AUTO_INCREMENT = 10000000;\n")
Coverages.write("INSERT INTO Coverages (PlanID, ServiceCode) VALUES\n")
Bulk_Database.write("DELETE FROM Coverages;\n")
Bulk_Database.write("ALTER TABLE Coverages AUTO_INCREMENT = 10000000;\n")
Bulk_Database.write("INSERT INTO Coverages (PlanID, ServiceCode) VALUES\n")

PlanID = 10000000;
PlanID -= 1;

ServiceCode = 10000000 + db_services_size;

#Coverage
for x in range(db_coverage_size):

	#PlanID
	if PlanID >= (10000000 + db_insplans_size - 1):
		PlanID = PlanID = 10000000 - 1;

	PlanID += 1

	#ServiceCode
	if (ServiceCode < 10000000):
		ServiceCode = 10000000 + db_services_size;

	ServiceCode -= 1

	#Data
	Coverages.write("('{}','{}')".format(PlanID, ServiceCode))
	if (x != db_coverage_size - 1):
		Coverages.write(",\n")
	Bulk_Database.write("('{}','{}')".format(PlanID, ServiceCode))
	if (x != db_coverage_size - 1):
		Bulk_Database.write(",\n")

Coverages.write(";")
Bulk_Database.write(";\n")

########################################################################

print("Database Complete")

Bulk_Database.close()

Patients.close()
InsPlans.close()
InsCompany.close()
Coverages.close()
IncludedService.close()
IncludedProducts.close()
Services.close()
ServiceCosts.close()
Membership.close()
ProductTransactions.close()
ServiceProviders.close()
ServiceRecords.close()
MedicalProducts.close()
ProductCosts.close()
Pharmacies.close()
ServiceProducts.close()

name_database.close()
address_database.close()
email_database.close()
employer_database.close()
company_database.close()
treatment_database.close()
services_database.close()
pharmacies_database.close()
institutions_database.close()
specialty_database.close()
products_database.close()
manufacturers_database.close()