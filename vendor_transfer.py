#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Created on Mon May 13 16:00:58 2019

@author: cmeehan
"""
import mysql.connector



remote_db = mysql.connector.connect(
        host = 'ns8140.hostgator.com',
        user = 'cmeehan',
        passwd = 'Ilovetaylor2016!',
        database="cmeehan_utterfare")

local_db = mysql.connector.connect(
        host = 'localhost', 
        user = 'root', 
        passwd = 'root', 
        database = 'utterfare')

remote_cursor = remote_db.cursor()
local_cursor = local_db.cursor()

sql = "SELECT ID, COMPANY_NAME, LATITUDE, LONGITUDE, PRIMARY_ADDRESS, SECONDARY_ADDRESS, CITY, STATE, ZIP, PRIMARY_PHONE, PROFILE_PICTURE FROM 290sc_customer"

remote_cursor.execute(sql)

results = remote_cursor.fetchall()

for row in results:
    print(row)
    vendors_sql = 'INSERT INTO vendors(vendor_id, vendor_name, latitude, longitude)\
    VALUES(%i, "%s", "%s", "%s")'


    local_cursor.execute(vendors_sql % (row[0], row[1].strip().replace("'", "\'"), row[2], row[3]))
    
    
    local_db.commit()
    
    
    vendor_meta_sql = 'INSERT INTO vendor_meta(vendor_id, meta_keyword, meta_value) VALUES ("%s", "%s", "%s")'
    
    address = row[4].strip().replace("'", "\'")
    if row[5] is not None:
        address += ", " + row[5].strip().replace("'", "\'")
    address += ", " + row[6].strip().replace("'", "\'")
    address += ", " + row[7].strip().replace("'", "\'")
    address += " " + row[8]
    
    local_cursor.execute(vendor_meta_sql %(row[0], '_address', address ))

    local_db.commit()
    local_cursor.execute(vendor_meta_sql %(row[0], '_telephone', row[9]))

    local_db.commit()
    
    local_cursor.execute(vendor_meta_sql %(row[0], '_profile_picture', row[10]))
    
    local_db.commit()
    